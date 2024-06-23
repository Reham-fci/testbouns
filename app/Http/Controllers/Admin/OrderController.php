<?php

namespace App\Http\Controllers\Admin;

require __DIR__ . '/SimpleXLSXGen.php';

use App\CPU\Helpers;
use App\CPU\OrderManager;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\AdminWallet;
use App\Model\BusinessSetting;
use App\Model\DeliveryMan;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\OrderTransaction;
use App\Model\Product;
use App\Model\Seller;
use App\Model\SellerWallet;
use App\Model\ShippingAddress;
use App\Model\ShippingMethod;
use App\Model\Shop;
use Barryvdh\DomPDF\Facade as PDF;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function App\CPU\translate;
use App\CPU\CustomerManager;
use App\CPU\Convert;
use App\CPU\BackEndHelper;
use App\Model\Salesperson;
use SimpleXLSXGen;

class OrderController extends Controller
{
    public function list(Request $request, $status)
    {

        $search = $request['search'];
        $from = $request['from'];
        $to = $request['to'];

        $salesPersonId = isset($request['salesPersonId']) ? $request['salesPersonId'] : 0;
        $Salesperson = Salesperson::where('is_active', 1)->get();
        if (session()->has('show_inhouse_orders') && session('show_inhouse_orders') == 1) {
            $query = Order::whereHas('details', function ($query) {
                $query->whereHas('product', function ($query) {
                    $query->where('added_by', 'admin');
                });
            })->with(['customer']);

            if ($status != 'all') {
                $orders = $query->where(['order_status' => $status]);
            } else {
                $orders = $query;
            }
        } else {
            if ($status != 'all') {
                $orders = Order::with(['customer'])->where(['order_status' => $status]);
            } else {
                $orders = Order::with(['customer']);
            }
        }
        Order::where(['checked' => 0])->update(['checked' => 1]);


        $key = explode(' ', $request['search']);
        $orders = $orders->when($request->has('search') && $search != null, function ($q) use ($key) {
            $q->where(function ($qq) use ($key) {
                foreach ($key as $value) {
                    $qq->where('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_ref', 'like', "%{$value}%");
                }
            });
        })->when($from != null, function ($dateQuery) use ($from, $to) {
            $dateQuery->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);
        });


        if ($request['salesPersonId']) {

            $query_param['salesPersonId'] =  $request['salesPersonId'];
            $customer_id = DB::table('users')->select('id')->where('salesPersonId', $request['salesPersonId']);
            $orders->whereIn('customer_id', $customer_id);
        }

        $orders = $orders->where('order_type', 'default_type')->orderBy('id', 'desc')->paginate(Helpers::pagination_limit())->appends(['search' => $request['search'], 'from' => $request['from'], 'to' => $request['to']]);
        return view('admin-views.order.list', compact('orders', 'status', 'search', 'from', 'to' ,'salesPersonId' ,'Salesperson'));
    }
    public function export(Request $request, $status)
    {

        $search = $request['search'];
        $from = $request['from'];
        $to = $request['to'];

        if (session()->has('show_inhouse_orders') && session('show_inhouse_orders') == 1) {
            $query = Order::whereHas('details', function ($query) {
                $query->whereHas('product', function ($query) {
                    $query->where('added_by', 'admin');
                });
            })->with(['customer']);

            if ($status != 'all') {
                $orders = $query->where(['order_status' => $status]);
            } else {
                $orders = $query;
            }
        } else {
            if ($status != 'all') {
                $orders = Order::with(['customer'])->where(['order_status' => $status]);
            } else {
                $orders = Order::with(['customer']);
            }
        }
        Order::where(['checked' => 0])->update(['checked' => 1]);


        $key = explode(' ', $request['search']);
        $orders = $orders->when($request->has('search') && $search != null, function ($q) use ($key) {
            $q->where(function ($qq) use ($key) {
                foreach ($key as $value) {
                    $qq->where('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_ref', 'like', "%{$value}%");
                }
            });
        })->when($from != null, function ($dateQuery) use ($from, $to) {
            $dateQuery->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);
        });


        if ($request['salesPersonId']) {


            $customer_id = DB::table('users')->select('id')->where('salesPersonId', $request['salesPersonId']);
            $orders->whereIn('customer_id', $customer_id);
        }

        $orders = $orders->where('order_type', 'default_type')->orderBy('id', 'desc')->get();
        // ->appends(['search' => $request['search'], 'from' => $request['from'], 'to' => $request['to']]);
        $exportData = [];
        $exportData[] = [
            translate('SL'),
            translate('Order'),
            translate('Date'),
            translate('customer_name'),
            translate('Phone'),
            translate('Status'),
            translate('Total'),
            translate('Order') . ' ' . translate('Status'),
            // translate('Action'),
            "Link GPS",
            // "Link GPS"
        ];





        foreach ($orders as $key => $order) {

            $address = json_decode($order['shipping_address_data']);
            $exportData[] = [
                $key + 1,
                $order['id'],
                date('d M Y', strtotime($order['created_at'])),
                $order->customer ? $order->customer['f_name'] . ' ' . $order->customer['l_name'] : translate('invalid_customer_data'),
                $order->customer ? $order->customer['phone'] : '',
                ($order->payment_status == 'paid') ? translate('paid') : translate('unpaid'),
                \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($order->order_amount)),
                $order['order_status'],
                "https://www.google.com/maps?q=".$address->latitude.",".$address->longitude."",
                // "https://www.google.com/maps?q=".$address->longitude.",".$address->latitude.""

            ];
        }
        SimpleXLSXGen::fromArray($exportData)->downloadAs('orders.xlsx');
    }

    public function details($id)
    {
        $order = Order::with('details', 'shipping', 'seller')->where(['id' => $id])->first();
        if(is_numeric($order->shippingAddress->city)){
            $city = DB::table('governorates')->where('id','=', $order->shippingAddress->city)->first();
            $order->shippingAddress->city = $city->governorate_name_ar;
        }
        if(is_numeric($order->shippingAddress->zip)){
            $zip = DB::table('cities')->where('id','=', $order->shippingAddress->zip)->first();
            $order->shippingAddress->zip = $zip->city_name_ar;
        }
        if($order->billingAddress){

            if(is_numeric($order->billingAddress->city)){
                $city = DB::table('governorates')->where('id','=', $order->billingAddress->city)->first();
                $order->billingAddress->city = $city->governorate_name_ar;
            }
            if(is_numeric($order->billingAddress->zip)){
                $zip = DB::table('cities')->where('id','=', $order->billingAddress->zip)->first();
                $order->billingAddress->zip = $zip->city_name_ar;
            }
        }
        $linked_orders = Order::where(['order_group_id' => $order['order_group_id']])
            ->whereNotIn('order_group_id', ['def-order-group'])
            ->whereNotIn('id', [$order['id']])
            ->get();

        $shipping_method = Helpers::get_business_settings('shipping_method');
        $delivery_men = DeliveryMan::where('is_active', 1)->when($order->seller_is == 'admin', function ($query) {
            $query->where(['seller_id' => 0]);
        })->when($order->seller_is == 'seller' && $shipping_method == 'sellerwise_shipping', function ($query) use ($order) {
            $query->where(['seller_id' => $order['seller_id']]);
        })->when($order->seller_is == 'seller' && $shipping_method == 'inhouse_shipping', function ($query) use ($order) {
            $query->where(['seller_id' => 0]);
        })->get();

        $shipping_address = ShippingAddress::find($order->shipping_address);
        if ($order->order_type == 'default_type') {
            return view('admin-views.order.order-details', compact('shipping_address', 'order', 'linked_orders', 'delivery_men'));
        } else {
            return view('admin-views.pos.order.order-details', compact('order'));
        }
    }

    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::find($order_id);
        /*if($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled' || $order->order_status == 'scheduled') {
            return response()->json(['status' => false], 200);
        }*/
        $order->delivery_man_id = $delivery_man_id;
        $order->delivery_type = 'self_delivery';
        $order->delivery_service_name = null;
        $order->third_party_delivery_tracking_id = null;
        $order->save();

        $fcm_token = $order->delivery_man->fcm_token;
        $value = Helpers::order_status_update_message('del_assign') . " ID: " . $order['id'];
        try {
            if ($value != null) {
                $data = [
                    'title' => translate('order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {
            Toastr::warning(translate('Push notification failed for DeliveryMan!'));
        }

        return response()->json(['status' => true], 200);
    }

    public function status(Request $request)
    {
        $order = Order::find($request->id);
        $old_order_status = $order['order_status'];
        $new_order_status = $request->order_status;
        $old_payment_status = $order['payment_status'];







        // $wallet_status = Helpers::get_business_settings('wallet_status');
        // $loyalty_point_status = Helpers::get_business_settings('loyalty_point_status');

        if ($request->order_status == 'delivered' && $order->payment_status != 'paid') {

            return response()->json(['payment_status' => 0], 200);
        }
        $fcm_token = $order->customer->cm_firebase_token;
        $value = Helpers::order_status_update_message($request->order_status);
        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {
        }


        try {
            $fcm_token_delivery_man = $order->delivery_man->fcm_token;
            if ($value != null) {
                $data = [
                    'title' => translate('order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token_delivery_man, $data);
            }
        } catch (\Exception $e) {
        }

        $order->order_status = $request->order_status;
        $order->order_comment = $request->order_comment;
        OrderManager::stock_update_on_order_status_change($order, $request->order_status);
        $order->save();
        if($order->wasChanged("order_status")){
            if($order->convertToMoney == 0){
                if(
                    $old_order_status != "delivered"
                    && $new_order_status == "delivered"
                    && $old_payment_status == "paid"
                ){
                    CustomerManager::ConvertOrderAmountToWallet($order);
                }
                else if(
                        $old_order_status == "delivered"
                        && $new_order_status != "delivered"
                        // && $old_payment_status == "paid"
                    ){

                    $walletMoney = CustomerManager::RemoveOrderAmountToWallet($order);
                    $order->convertToMoney = $walletMoney ? 0 : 1;
                    $order->save();
                }


                if(in_array($new_order_status, ['failed' , 'returned' , 'canceled']) && $old_payment_status !== "paid"){

                    if($order->walletAmount > 0){

                        if(DB::table('wallet_transaction_order')->where('orderId' , $order->id)->delete()){
                            $wallet_transaction = CustomerManager::create_wallet_transaction($order['customer_id'],$order->walletAmount,'return_money_order','return_money_order');
                            $order->walletAmount = 0 ;
                            $order->save();
                        }
                    }

                }

            }
        }
        // if ($loyalty_point_status == 1) {
        //     if ($request->order_status == 'delivered' && $order->payment_status == 'paid') {
        //         CustomerManager::create_loyalty_point_transaction($order->customer_id, $order->id, Convert::default($order->order_amount - $order->shipping_cost), 'order_place');
        //     }
        // }
        $transaction = OrderTransaction::where(['order_id' => $order['id']])->first();
        if (isset($transaction) && $transaction['status'] == 'disburse') {
            return response()->json($request->order_status);
        }



        if ($request->order_status == 'delivered' && $order['seller_id'] != null) {
            OrderManager::wallet_manage_on_order_status_change($order, 'admin');
            OrderDetail::where('order_id', $order->id)->update(
                ['delivery_status' => 'delivered']
            );
        }

        return response()->json($request->order_status);
    }




    public function payment_status(Request $request)
    {
        if ($request->ajax()) {
            $order = Order::find($request->id);

            $old_order_status = $order['order_status'];
            $new_payment_status = $request->payment_status;
            $old_payment_status = $order['payment_status'];


            $order->payment_status = $request->payment_status;
            $order->save();
            $data = $request->payment_status;

            if($order->wasChanged("payment_status"))
            {
                if($order->convertToMoney == 0)
                {
                    if(
                        $old_payment_status != "paid"
                        && $new_payment_status == "paid"
                        && $old_order_status == "delivered"
                    ){
                        CustomerManager::ConvertOrderAmountToWallet($order);

                    }

                    // else if(
                    //     $old_payment_status == "paid"
                    //     && $new_payment_status != "paid"
                    //     && $old_order_status == "delivered"
                    // ){
                    //     $walletMoney = CustomerManager::RemoveOrderAmountToWallet($order);
                    //     $order->convertToMoney = $walletMoney ? 0 : 1;
                    //     $order->save();
                    // }
                }
            }


            return response()->json($data);
        }
    }

    public function generate_invoice2($id)
    {
        $order = Order::with('seller')->with('shipping')->with('details')->where('id', $id)->first();
        $seller = Seller::find($order->details->first()->seller_id);
        $data["email"] = $order->customer != null ? $order->customer["email"] : translate('email_not_found');
        $data["client_name"] = $order->customer != null ? $order->customer["f_name"] . ' ' . $order->customer["l_name"] : translate('customer_not_found');
        $data["order"] = $order;

        $mpdf_view = \View::make('admin-views.order.invoice')->with('order', $order)->with('seller', $seller);
        if(isset($_REQUEST['test_dev'])){
            echo $mpdf_view;exit;
        }
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_', $order->id);
    }

    public function generate_invoice($id)
    {
        $order = Order::with('seller')->with('shipping')->with('details')->where('id', $id)->first();
        $seller = Seller::find($order->details->first()->seller_id);
        $data["email"] = $order->customer != null ? $order->customer["email"] : translate('email_not_found');
        $data["client_name"] = $order->customer != null ? $order->customer["f_name"] . ' ' . $order->customer["l_name"] : translate('customer_not_found');
        $data["order"] = $order;

        $mpdf_view = \View::make('admin-views.order.invoice2')->with('order', $order)->with('seller', $seller);

        Helpers::gen_mpdf($mpdf_view, 'order_invoice_', $order->id);
    }

    public function inhouse_order_filter()
    {
        if (session()->has('show_inhouse_orders') && session('show_inhouse_orders') == 1) {
            session()->put('show_inhouse_orders', 0);
        } else {
            session()->put('show_inhouse_orders', 1);
        }
        return back();
    }
    public function update_deliver_info(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->delivery_type = 'third_party_delivery';
        $order->delivery_service_name = $request->delivery_service_name;
        $order->third_party_delivery_tracking_id = $request->third_party_delivery_tracking_id;
        $order->delivery_man_id = null;
        $order->save();

        Toastr::success(translate('updated_successfully!'));
        return back();
    }

    public function order_details_update(Request $request, $id)
    {
        $order = Order::with('details', 'shipping', 'seller')->where(['id' => $id])->first();

        $linked_orders = Order::where(['order_group_id' => $order['order_group_id']])
            ->whereNotIn('order_group_id', ['def-order-group'])
            ->whereNotIn('id', [$order['id']])
            ->get();
            // SELECT `id`, `coupon_type`, `title`, `code`, `start_date`, `expire_date`, `min_purchase`, `max_discount`, `discount`, `discount_type`, `status`, `created_at`, `updated_at`, `limit`, `forall`, `FromRegisterDate`, `FromOrderTimes`, `Fromprice`, `ToRegisterDate`, `ToOrderTimes`, `Toprice`, `city`, `area`, `type`, `qty` FROM `coupons` WHERE 1
        $today = date('Y-m-d');
        $coupons = DB::table('coupons')
        ->where('start_date' , '<=' , $today)
        ->where('expire_date' , '>=' , $today)
        ->get();
        // dd($coupons);
        $shipping_method = Helpers::get_business_settings('shipping_method');
        $delivery_men = DeliveryMan::where('is_active', 1)->when($order->seller_is == 'admin', function ($query) {
            $query->where(['seller_id' => 0]);
        })->when($order->seller_is == 'seller' && $shipping_method == 'sellerwise_shipping', function ($query) use ($order) {
            $query->where(['seller_id' => $order['seller_id']]);
        })->when($order->seller_is == 'seller' && $shipping_method == 'inhouse_shipping', function ($query) use ($order) {
            $query->where(['seller_id' => 0]);
        })->get();
        $products = Product::where('status', 1)
            ->when($request->has('category_id') && $request['category_id'] != 0, function ($query) use ($request) {
                $query->whereJsonContains('category_ids', [[['id' => (string)$request['category_id']]]]);
            })->get();
        // dd( $order );
        // dd($products[0]->variation);
        $shipping_address = ShippingAddress::find($order->shipping_address);
        if ($order->order_type == 'default_type') {
            return view('admin-views.order.order-update', compact('coupons','shipping_address', 'order', 'linked_orders', 'delivery_men', 'products'));
        } else {
            return view('admin-views.pos.order.order-update', compact('order', 'products'));
        }
    }

    public function get_variant(Request $request)
    {
        $id = $request->input('id');

        $v = Product::findOrFail($id);
        return json_decode($v->variation);
    }

    public function add_product(Request $request)
    {
        $id = $request->input('p_id');
        $variant = $request->input('v_id');

        $p = Product::findOrFail($id);

        $va = json_decode($p->variation);
        foreach ($va as $v) {
            if ($variant == $v->type) {
                $data['variant'] = $v;
                break;
            }
        }

        // $p->name= substr( $p->name,0,30);

        $data['product'] = $p;
        return $data;
    }

    public function order_details_update_now(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $input = $request->all();

        $data = [];
        $total = 0;
        $total_descount = 0;
        $_products = [];
        for ($i = 0; $i < count($input['qty']); $i++) {

            $data[$i]['qty'] = $input['qty'][$i];
            $data[$i]['d_id'] = $input['d_id'][$i];
            $data[$i]['p_id'] = $input['p_id'][$i];
            $data[$i]['variant'] = $input['variant_id'][$i];
            $data[$i]['price'] = $input['price'][$i];
        }
        $ids = [];
        $update = false;
        try {
            DB::beginTransaction();
            $total_tax_amount = 0;
            $product_price = 0;
            $order_details = [];
            $product_subtotal = 0;
            foreach ($data as $c) {
                $product = Product::find($c['p_id']);
                $_products[] = $product;
                $total += (($c['qty'] * $c['price']));
                if ($c['d_id'] != -1)
                {
                    $ids[] = $c['d_id'];
                    $o_d = OrderDetail::findOrFail($c['d_id']);
                    $total -= ($o_d->discount);
                    $total_descount += ($o_d->discount * $c['qty']);

                    if ($c['qty'] != $o_d->qty) {
                        $price = $c['price'];
                        $discount_on_product = 0;
                        $o_d->update([
                            'qty' => $c['qty'],
                            'tax' => Helpers::tax_calculation($price, $product['tax'], $product['tax_type']) * $c['qty'],
                            'updated_at' => now(),
                        ]);

                        $product_subtotal = ($c['price']) * $c['qty'];
                        $discount_on_product += ($o_d->discount * $c['qty']);
                        $total_tax_amount += $o_d->tax * $c['qty'];
                        $product_price += $product_subtotal - $discount_on_product;

                        if ($c['variant'] != null) {
                            $type = $c['variant'];
                            $var_store = [];

                            foreach (json_decode($product['variation'], true) as $var) {
                                if ($type == $var['type']) {
                                    $var['qty'] -= $c['qty'];
                                    $var['qty'] += $o_d->qty;
                                }
                                array_push($var_store, $var);
                            }
                            Product::where(['id' => $product['id']])->update([
                                'variation' => json_encode($var_store),
                            ]);
                        }

                        Product::where(['id' => $product['id']])->update([
                            'current_stock' => $product['current_stock'] - $c['qty'] + $o_d->qty
                        ]);
                    }
                } else {


                    $total -= ($product->discount * $c['qty']);
                    $total_descount += ($product->discount * $c['qty']);
                    if (is_array($c)) {
                        $discount_on_product = 0;

                        $product_subtotal = ($c['price']) * $c['qty'];
                        $discount_on_product += 0;
                        // $discount_on_product += ($c['discount'] * $c['quantity']);


                        if ($product) {
                            $price = $c['price'];

                            $or_d = [
                                'order_id' => $order->id,
                                'product_id' => $c['p_id'],
                                'product_details' => $product,
                                'qty' => $c['qty'],
                                'price' => $price,
                                'seller_id' => $product['user_id'],
                                'tax' => Helpers::tax_calculation($price, $product['tax'], $product['tax_type']) * $c['qty'],
                                // 'discount' => $c['discount']*$c['quantity'],
                                'discount' => 0,
                                'discount_type' => 'discount_on_product',
                                "delivery_status" => "pending",
                                "payment_status" => "unpaid",
                                'variation' => json_decode($product['variations']),
                                'variant' => $c['variant'],
                                'variation' => $product['variations'],
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            $total_tax_amount += $or_d['tax'] * $c['qty'];
                            $product_price += $product_subtotal - $discount_on_product;
                            $order_details[] = $or_d;


                            if ($c['variant'] != null) {
                                $type = $c['variant'];
                                $var_store = [];

                                foreach (json_decode($product['variation'], true) as $var) {
                                    if ($type == $var['type']) {
                                        $var['qty'] -= $c['qty'];
                                    }
                                    array_push($var_store, $var);
                                }
                                Product::where(['id' => $product['id']])->update([
                                    'variation' => json_encode($var_store),
                                ]);
                            }

                            Product::where(['id' => $product['id']])->update([
                                'current_stock' => $product['current_stock'] - $c['qty']
                            ]);

                            $id = DB::table('order_details')->insertGetId($or_d);
                        }
                    }
                    $ids[] = $id;
                }
            }

            // $total_price = $product_price;
            if(isset($_REQUEST['Dev_test'])){
                echo "<br>";
                print_r($total);
                echo "<br>";
                print_r($order['discount_amount']);
                echo "<br>";
                print_r($total_descount);
                echo "<br><pre>";
                // print_r($_products);
                echo "<br>";
                print_r($total - $order['discount_amount']);
            }
            if($request->couponValue){
                $coupon = DB::table('coupons')->where('id' , $request->couponValue)->first();
                if($coupon){
                    if($coupon->discount_type == "percentage"){

                    $order->discount_amount = ($coupon->discount * $total) / 100;
                    $order->discount_amount = ($order->discount_amount > $coupon->max_discount) ? $coupon->max_discount : $order->discount_amount;
                    }
                    else{

                        $order->discount_amount = $coupon->discount;
                    }
                    $order->discount_type = 'coupon_discount';
                    $order->coupon_code =  $coupon->code;
                    $order->coupon_discount = $coupon->discount;
                    $order->coupon_discount_type =  $coupon->discount_type;
                    $order->coupon_max_discount =  $coupon->max_discount;
                }

            }
            else if($order->coupon_discount_type){

                if($order->coupon_discount_type == "percentage"){

                $order->discount_amount = ($order->coupon_discount * $total) / 100;
                $order->discount_amount = ($order->discount_amount > $order->coupon_max_discount) ? $coupon->max_discount : $order->discount_amount;
                // dd($order);
                }
                else{

                    // $order->discount_amount = $coupon->coupon_discount;
                }
            }
            // $order->update([
            //     'order_amount' => $total - $order['discount_amount'],
            //     'updated_at' => now(),
            // ]);

            $order->order_amount = $total - $order['discount_amount'];
            $order->updated_at = now();


            $order->save();

            $out = OrderDetail::whereNotIn('id', $ids)->where('order_id', $order->id)->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }

        // echo $total - $order['discount_amount'];
        // exit();

            if(isset($_REQUEST['Dev_test'])){
                exit();exit();
            }
        return back();
    }
}
