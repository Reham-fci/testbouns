<?php

namespace App\CPU;

use App\Model\Admin;
use App\Model\AdminWallet;
use App\Model\Cart;
use App\Model\CartShipping;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\OrderTransaction;
use App\Model\Product;
use App\Model\Seller;
use App\Model\SellerWallet;
use App\Model\ShippingType;
use App\Model\ShippingAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class OrderManager
{
    public static function track_order($order_id)
    {
        $order = Order::where(['id' => $order_id])->first();
        $order['billing_address_data'] = json_decode($order['billing_address_data']);
        $order['shipping_address_data'] = json_decode($order['shipping_address_data']);
        return $order;
    }

    public static function gen_unique_id()
    {
        return rand(1000, 9999) . '-' . Str::random(5) . '-' . time();
    }

    public static function order_summary($order)
    {
        $sub_total = 0;
        $total_tax = 0;
        $total_discount_on_product = 0;
        foreach ($order->details as $key => $detail) {
            $sub_total += $detail->price * $detail->qty;
            $total_tax += $detail->tax;
            $total_discount_on_product += $detail->discount;
        }
        $total_shipping_cost = $order['shipping_cost'];
        return [
            'subtotal' => $sub_total,
            'total_tax' => $total_tax,
            'total_discount_on_product' => $total_discount_on_product,
            'total_shipping_cost' => $total_shipping_cost,
        ];
    }

    public static function stock_update_on_order_status_change($order, $status)
    {
        if ($status == 'returned' || $status == 'failed' || $status == 'canceled') {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 1) {
                    // $product = Product::find($detail['product_id']);
                    // SELECT `id`, `productId`, `type`, `price`, `sku`, `purchase_price`, `tax`, `discount`, `discount_type`, `tax_type`, `shipping_cost`, `multiply_qty`, `numberOfPieces`, `qty`, `description`, `order` FROM `product_variations` WHERE 1
                    $type = $detail['variant'];
                    $_productVariant = DB::table('product_variations')
                    ->where('productId' , $detail['product_id'])
                    ->where('type' , $type)
                    ->first();
                    $var_store = [];
                    // foreach (json_decode($product['variation'], true) as $var) {
                    //     if ($type == $var['type']) {
                    //         $var['qty'] += $detail['qty'];
                    //     }
                    //     array_push($var_store, $var);
                    // }
                    $_qty = $detail['qty'] ;
                    if($_productVariant){
                        $_qty = $detail['qty'] * $_productVariant->numberOfPieces;
                    }
                    
                    Product::where(['id' => $detail['product_id']])->update([
                        // 'variation' => json_encode($var_store),
                        // 'current_stock' => $product['current_stock'] + $detail['qty'],
                        'current_stock' => DB::raw('current_stock + ' . $_qty),
                    ]);
                    OrderDetail::where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 0
                    ]);
                }
            }
        } else {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 0) {
                    $product = Product::find($detail['product_id']);

                    //check stock
                    /*foreach ($order->details as $c) {
                        $product = Product::find($c['product_id']);
                        $type = $detail['variant'];
                        foreach (json_decode($product['variation'], true) as $var) {
                            if ($type == $var['type'] && $var['qty'] < $c['qty']) {
                                Toastr::error('Stock is insufficient!');
                                return back();
                            }
                        }
                    }*/
                    
                    $type = $detail['variant'];
                    $_productVariant = DB::table('product_variations')
                    ->where('productId' , $detail['product_id'])
                    ->where('type' , $type)
                    ->first();
                    $_qty = $detail['qty'] ;
                    if($_productVariant){
                        $_qty = $detail['qty'] * $_productVariant->numberOfPieces;
                    }
                    

                    $type = $detail['variant'];
                    $var_store = [];
                    // foreach (json_decode($product['variation'], true) as $var) {
                    //     if ($type == $var['type']) {
                    //         $var['qty'] -= $detail['qty'];
                    //     }
                    //     array_push($var_store, $var);
                    // }
                    Product::where(['id' => $product['id']])->update([
                        // 'variation' => json_encode($var_store),
                        // 'current_stock' => $product['current_stock'] - $detail['qty'],
                        'current_stock' => $product['current_stock'] - $_qty,
                    ]);
                    OrderDetail::where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 1
                    ]);
                }
            }
        }
    }

    public static function wallet_manage_on_order_status_change($order, $received_by)
    {
        $order = Order::find($order['id']);
        $order_summary = OrderManager::order_summary($order);
        $order_amount = $order_summary['subtotal'] - $order_summary['total_discount_on_product'] - $order['discount_amount'];
        $commission = Helpers::sales_commission($order);
        $shipping_model = Helpers::get_business_settings('shipping_method');

        if (AdminWallet::where('admin_id', 1)->first() == false) {
            DB::table('admin_wallets')->insert([
                'admin_id' => 1,
                'withdrawn' => 0,
                'commission_earned' => 0,
                'inhouse_earning' => 0,
                'delivery_charge_earned' => 0,
                'pending_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (SellerWallet::where('seller_id', $order['seller_id'])->first() == false) {
            DB::table('seller_wallets')->insert([
                'seller_id' => $order['seller_id'],
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($order['payment_method'] == 'cash_on_delivery') {
            DB::table('order_transactions')->insert([
                'transaction_id' => OrderManager::gen_unique_id(),
                'customer_id' => $order['customer_id'],
                'seller_id' => $order['seller_id'],
                'seller_is' => $order['seller_is'],
                'order_id' => $order['id'],
                'order_amount' => $order_amount,
                'seller_amount' => $order_amount - $commission,
                'admin_commission' => $commission,
                'received_by' => $received_by,
                'status' => 'disburse',
                'delivery_charge' => $order['shipping_cost'],
                'tax' => $order_summary['total_tax'],
                'delivered_by' => $received_by,
                'payment_method' => $order['payment_method'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $wallet = AdminWallet::where('admin_id', 1)->first();
            $wallet->commission_earned += $commission;
            if ($shipping_model == 'inhouse_shipping') {
                $wallet->delivery_charge_earned += $order['shipping_cost'];
            }
            $wallet->save();

            if ($order['seller_is'] == 'admin') {
                $wallet = AdminWallet::where('admin_id', 1)->first();
                $wallet->inhouse_earning += $order_amount;
                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                }
                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            } else {
                $wallet = SellerWallet::where('seller_id', $order['seller_id'])->first();
                $wallet->commission_given += $commission;
                $wallet->total_tax_collected += $order_summary['total_tax'];

                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                    $wallet->collected_cash += $order['order_amount']; //total order amount
                } else {
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'];
                }

                $wallet->save();
            }
        } else {
            $transaction = OrderTransaction::where(['order_id' => $order['id']])->first();
            $transaction->status = 'disburse';
            $transaction->save();

            $wallet = AdminWallet::where('admin_id', 1)->first();
            $wallet->commission_earned += $commission;
            $wallet->pending_amount -= $order['order_amount'];
            if ($shipping_model == 'inhouse_shipping') {
                $wallet->delivery_charge_earned += $order['shipping_cost'];
            }
            $wallet->save();

            if ($order['seller_is'] == 'admin') {
                $wallet = AdminWallet::where('admin_id', 1)->first();
                $wallet->inhouse_earning += $order_amount;
                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                }
                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            } else {
                $wallet = SellerWallet::where('seller_id', $order['seller_id'])->first();
                $wallet->commission_given += $commission;

                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'] + $order['shipping_cost'];
                } else {
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'];
                }

                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            }
        }
    }

    public static function generate_order($data)
    {
        $order_id = 100000 + Order::all()->count() + 1;
        if (Order::find($order_id)) {
            $order_id = Order::orderBy('id', 'DESC')->first()->id + 1;
        }
        $address_id = session('address_id') ? session('address_id') : null;
        $billing_address_id = session('billing_address_id') ? session('billing_address_id') : null;
        $coupon_code = session()->has('coupon_code') ? session('coupon_code') : 0;
        $discount = session()->has('coupon_discount') ? session('coupon_discount') : 0;
        $order_note = session()->has('order_note') ? session('order_note') : null;

        $req = array_key_exists('request', $data) ? $data['request'] : null;
        $_coupon_coupon = null;
        if ($req != null) {
            $walletAmount = $req['walletAmount'] ;
            if (session()->has('coupon_code') == false) {
                $coupon_code = $req->has('coupon_code') ? $req['coupon_code'] : null;
                if($req->has('coupon_code')){
                    $_coupon = Helpers::coupon_discount2($req);
                    $_coupon_coupon = $_coupon['coupon'];
                    $discount = $req->has('coupon_code') ? $_coupon['discount'] : $discount;
                }
            }
            if (session()->has('address_id') == false) {
                $address_id = $req->has('address_id') ? $req['address_id'] : null;
            }
        }
        else{
            $walletAmount = $data['walletAmount'] ;
            
            if($coupon_code){
                $_coupon_coupon = Helpers::coupon_discount_for_web($coupon_code);
            }
        }
        $user = Helpers::get_customer($req);

        if ($discount > 0) {
            $discount = round($discount / count(CartManager::get_cart_group_ids($req)), 2);
        }

        $cart_group_id = $data['cart_group_id'];
        $seller_data = Cart::where(['cart_group_id' => $cart_group_id])->first();

        $shipping_method = CartShipping::where(['cart_group_id' => $cart_group_id])->first();
        if (isset($shipping_method)) {
            $shipping_method_id = $shipping_method->shipping_method_id;
        } else {
            $shipping_method_id = 0;
        }

        $shipping_model = Helpers::get_business_settings('shipping_method');
        if ($shipping_model == 'inhouse_shipping') {
            $admin_shipping = ShippingType::where('seller_id', 0)->first();
            $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
        } else {
            if ($seller_data->seller_is == 'admin') {
                $admin_shipping = ShippingType::where('seller_id', 0)->first();
                $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
            } else {
                $seller_shipping = ShippingType::where('seller_id', $seller_data->seller_id)->first();
                $shipping_type = isset($seller_shipping) == true ? $seller_shipping->shipping_type : 'order_wise';
            }
        }
        $or = [
            'id' => $order_id,
            'verification_code' => rand(100000, 999999),
            'customer_id' => $user->id,
            'seller_id' => $seller_data->seller_id,
            'seller_is' => $seller_data->seller_is,
            'customer_type' => 'customer',
            'payment_status' => $data['payment_status'],
            'order_status' => $data['order_status'],
            'payment_method' => $data['payment_method'],
            'transaction_ref' => $data['transaction_ref'],
            'order_group_id' => $data['order_group_id'],
            'discount_amount' => $discount,
            'discount_type' => $discount == 0 ? null : 'coupon_discount',
            'coupon_code' => $coupon_code,
            'order_amount' => CartManager::cart_grand_total($cart_group_id) - $discount - $walletAmount,
            'shipping_address' => $address_id,
            'shipping_address_data' => ShippingAddress::find($address_id),
            'billing_address' => $billing_address_id,
            'billing_address_data' => ShippingAddress::find($billing_address_id),
            'shipping_cost' => CartManager::get_shipping_cost($data['cart_group_id']),
            'shipping_method_id' => $shipping_method_id,
            'shipping_type' => $shipping_type,
            'created_at' => now(),
            'updated_at' => now(),
            'walletAmount' => $walletAmount,
            'order_note' => $order_note
        ];
        
        if($_coupon_coupon){
            
            $or['coupon_discount'] =  $_coupon_coupon->discount;
            $or['coupon_discount_type'] =  $_coupon_coupon->discount_type;
            $or['coupon_max_discount'] =  $_coupon_coupon->max_discount;
        }

        DB::table('orders')->insertGetId($or);

        foreach (CartManager::get_cart($data['cart_group_id']) as $c) {
            $product = Product::where(['id' => $c['product_id']])->first();
            $or_d = [
                'order_id' => $order_id,
                'product_id' => $c['product_id'],
                'seller_id' => $c['seller_id'],
                'product_details' => $product,
                'qty' => $c['quantity'],
                'price' => $c['price'],
                'tax' => $c['tax'] * $c['quantity'],
                'discount' => $c['discount'] * $c['quantity'],
                'discount_type' => 'discount_on_product',
                'variant' => $c['variant'],
                'variation' => $c['variations'],
                'delivery_status' => 'pending',
                'shipping_method_id' => null,
                'payment_status' => 'unpaid',
                'created_at' => now(),
                'updated_at' => now()
            ];
            $qty = $c['quantity'];
            if ($c['variant'] != null) {
                $type = $c['variant'];
                // $var_store = [];
                // foreach (json_decode($product['variation'], true) as $var) {
                //     if ($type == $var['type']) {
                //         $var['qty'] -= $c['quantity'];
                //     }
                //     array_push($var_store, $var);
                // }
                // Product::where(['id' => $product['id']])->update([
                //     'variation' => json_encode($var_store),
                // ]);
                
                $product_variation = DB::table("product_variations")->where('productId',$product->id)->where('type' , $type)->first();
                $product_variations_numberOfPieces = 1;
                if($product_variation){
                    $product_variations_numberOfPieces = $product_variation->numberOfPieces;
                }
                $qty = $c['quantity'] * $product_variations_numberOfPieces;
            }

            Product::where(['id' => $product['id']])->update([
                // 'current_stock' => $product['current_stock'] - $c['quantity']
                'current_stock' => $product['current_stock'] - $qty
            ]);

            DB::table('order_details')->insert($or_d);

        }

        if ($or['payment_method'] != 'cash_on_delivery') {
            $order = Order::find($order_id);
            $order_summary = OrderManager::order_summary($order);
            $order_amount = $order_summary['subtotal'] - $order_summary['total_discount_on_product'] - $order['discount'];
            $commission = Helpers::sales_commission($order);

            DB::table('order_transactions')->insert([
                'transaction_id' => OrderManager::gen_unique_id(),
                'customer_id' => $order['customer_id'],
                'seller_id' => $order['seller_id'],
                'seller_is' => $order['seller_is'],
                'order_id' => $order_id,
                'order_amount' => $order_amount,
                'seller_amount' => $order_amount - $commission,
                'admin_commission' => $commission,
                'received_by' => 'admin',
                'status' => 'hold',
                'delivery_charge' => $order['shipping_cost'],
                'tax' => $order_summary['total_tax'],
                'delivered_by' => 'admin',
                'payment_method' => $or['payment_method'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (AdminWallet::where('admin_id', 1)->first() == false) {
                DB::table('admin_wallets')->insert([
                    'admin_id' => 1,
                    'withdrawn' => 0,
                    'commission_earned' => 0,
                    'inhouse_earning' => 0,
                    'delivery_charge_earned' => 0,
                    'pending_amount' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('admin_wallets')->where('admin_id', $order['seller_id'])->increment('pending_amount', $order['order_amount']);
        }

        if ($seller_data->seller_is == 'admin') {
            $seller = Admin::find($seller_data->seller_id);
        } else {
            $seller = Seller::find($seller_data->seller_id);
        }

        try {
            $fcm_token = $user->cm_firebase_token;
            $seller_fcm_token = $seller->cm_firebase_token;
            if ($data['payment_method'] != 'cash_on_delivery') {
                $value = Helpers::order_status_update_message('confirmed');
            } else {
                $value = Helpers::order_status_update_message('pending');
            }

            if ($value) {
                $data = [
                    'title' => translate('order'),
                    'description' => $value,
                    'order_id' => $order_id,
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
                Helpers::send_push_notif_to_device($seller_fcm_token, $data);
            }

            $emailServices_smtp = Helpers::get_business_settings('mail_config');
            if ($emailServices_smtp['status'] == 0) {
                $emailServices_smtp = Helpers::get_business_settings('mail_config_sendgrid');
            }
            if ($emailServices_smtp['status'] == 1) {
                Mail::to($user->email)->send(new \App\Mail\OrderPlaced($order_id));
                Mail::to($seller->email)->send(new \App\Mail\OrderReceivedNotifySeller($order_id));
            }
        } catch (\Exception $exception) {
            //echo $exception;
        }

        return $order_id;
    }
}
