<?php

namespace App\Http\Controllers\Web;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        
        $sub_total = 0;
        $total_tax = 0;
        $qty = 0;
        $total_discount_on_product = 0;    
        $carts = CartManager::get_cart(CartManager::get_cart_group_ids());
        $shipping_cost = CartManager::get_shipping_cost();
        foreach($carts as $key => $cartItem)
        {
            $sub_total+=$cartItem['price']*$cartItem['quantity'];
            $total_tax+=$cartItem['tax']*$cartItem['quantity'];
            // $qty += $cartItem['quantity'];
            $qty += 1;
            $total_discount_on_product+=$cartItem['discount']*$cartItem['quantity'];
        }
        $total = $sub_total+$total_tax+$shipping_cost-$total_discount_on_product;
        $user = DB::table('users')->where('id' , auth('customer')->id())->first();
        $shipping_addresses = DB::table('shipping_addresses')->where('id' , session('address_id'))->first();
        $order = DB::table('orders')->select(DB::raw('count(*) as order_count'))
        ->where('customer_id' , auth('customer')->id())
        ->where('order_status' , 'delivered')->first();
      
        
        
        
        
        /*
            
                `forall`, 
                `FromRegisterDate`,
                `FromOrderTimes`,
                `Fromprice`,
                `ToRegisterDate`,
                `ToOrderTimes`,
                `Toprice`,
                `city`,
                `area`,
                `type`,
                `qty`

                'Fromprice <= '.$total.' and Toprice >= '.$total.' and Toprice != 0 '
                and FromOrderTimes <= '.$order.' and ToOrderTimes >= '.$order.' and ToOrderTimes != 0 '
                and (type = "'.$user->type.'" or type != "")
                and (city = "'.$request->city.'" or city != 0)
                and (area like "'.$request->area.',%" or area like "%,'.$request->area.'" or area like "%,'.$request->area.',%" or area = "'.$request->area.'" or area != 0)
                and (city like "'.$request->city.',%" or city like "%,'.$request->city.'" or city like "%,'.$request->city.',%" or city = "'.$request->city.'" or city != 0)
                and qty <= '.$qty.'
            */ 

        // print_r($shipping_addresses);exit();
        $couponLimit = Order::where('customer_id', auth('customer')->id())
            ->where('coupon_code', $request['code'])->count();
        
            
        // $coupon = Coupon::where(['code' => $request['code']])
        //     ->where('limit', '>', $couponLimit)
        //     ->where('status', '=', 1)
        //     ->where(
        //         'Fromprice <= '.$total.' and Toprice >= '.$total.' and Toprice != 0  and FromOrderTimes <= '.$order->order_count.' and ToOrderTimes >= '.$order->order_count.' and ToOrderTimes != 0  and (type = "'.$user->type.'" or type != "") and (area like "'.$shipping_addresses->city.',%" or area like "%,'.$shipping_addresses->city.'" or area like "%,'.$shipping_addresses->city.',%" or area = "'.$shipping_addresses->city.'" or area != 0) and (city like "'.$shipping_addresses->zip.',%" or city like "%,'.$shipping_addresses->zip.'" or city like "%,'.$shipping_addresses->zip.',%" or city = "'.$shipping_addresses->zip.'" or city != 0) and qty <= '.$qty.''
        //     , '','' , false)
        //     ->whereDate('start_date', '<=', date('Y-m-d'))
        //     ->whereDate('expire_date', '>=', date('Y-m-d'));
            // ->first();
        
            $sql = '
                select coupons.* 
                from coupons 
                where `limit` > '. $couponLimit .'
                and code = "'.$request['code'].'"
                and status  =  1
                and (qty <= '.$qty.' or qty is null)
                and (
                    forall =  1
                    or (
                        forall = 0  and (
                            (Fromprice <= '.$total.'  or Fromprice is null )
                            and (Toprice >= '.$total.'  or Toprice is null)
                            
                            
                            and (
                                FromRegisterDate  is null 
                                or FromRegisterDate <= "'.date('Y-m-d',strtotime($user->created_at)).'"
                            )
                            and (
                                ToRegisterDate is  null 
                                or ToRegisterDate >= "'.date('Y-m-d',strtotime($user->created_at)).'"
                            )
                            and (FromOrderTimes <= '.$order->order_count.' or FromOrderTimes is null)
                            and (ToOrderTimes >= '.$order->order_count.' or ToOrderTimes is null)
                            and (type = "'.$user->type.'" or type is null) 
                            and (
                                city like "'.$shipping_addresses->city.',%" 
                                or city like "%,'.$shipping_addresses->city.'" 
                                or city like "%,'.$shipping_addresses->city.',%" 
                                or city = "'.$shipping_addresses->city.'" 
                                or city = ""
                                ) 
                                and (
                                    area like "'.$shipping_addresses->zip.',%" 
                                    or area like "%,'.$shipping_addresses->zip.'" 
                                    or area like "%,'.$shipping_addresses->zip.',%" 
                                    or area = "'.$shipping_addresses->zip.'" 
                                    or area = ""
                                ) 
                            )  
                    )
                )
                and start_date <="'. date('Y-m-d').'" 
                and expire_date >= "' . date('Y-m-d') . '"
                
            ';
        // $sql = '
        //     select * 
        //     from coupons 
        //     where `limit` > '. $couponLimit .'
        //     and code = "'.$request->code.'"
        //     and status  =  1
        //     and (qty <= '.$qty.' or qty is null)
        //     and (
        //         forall =  1 
        //         or (
        //             forall = 0  and (
                    
        //                 (Fromprice <= '.$total.'  or Fromprice is null )
        //                 and (Toprice >= '.$total.'  or Toprice is null) 
        //                 and Toprice != 0  
        //                 and (
        //                     FromRegisterDate  is null 
        //                     or FromRegisterDate <= "'.date('Y-m-d',strtotime($user->created_at)).'"
        //                 )
        //                 and (
        //                     ToRegisterDate is  null  
        //                     or ToRegisterDate >= "'.date('Y-m-d',strtotime($user->created_at)).'"
        //                 )
                        
                        
        //                 and (FromOrderTimes <= '.$order->order_count.' or FromOrderTimes is null)
        //                 and (ToOrderTimes >= '.$order->order_count.' or ToOrderTimes is null)
                            
        //                 and (type = "'.$user->type.'" or type is null) 
        //                 and (
        //                     city like "'.$shipping_addresses->city.',%" 
        //                     or city like "%,'.$shipping_addresses->city.'" 
        //                     or city like "%,'.$shipping_addresses->city.',%" 
        //                     or city = "'.$shipping_addresses->city.'" 
        //                   or city = ""
        //                     ) 
        //                     and (
        //                         area like "'.$shipping_addresses->zip.',%" 
        //                         or area like "%,'.$shipping_addresses->zip.'" 
        //                         or area like "%,'.$shipping_addresses->zip.',%" 
        //                         or area = "'.$shipping_addresses->zip.'" 
        //                       or area = ""
        //                     ) 
        //                 )  
        //         )
        //     )
        //     and start_date <="'. date('Y-m-d').'" 
        //     and expire_date >= "' . date('Y-m-d') . '"
            
        // ';
        $coupon = DB::select($sql);
        // $coupon_obj = $coupon[0];
        if ($coupon) {
            $coupon = json_decode(json_encode($coupon[0]), true);
            $total = 0;
            foreach (CartManager::get_cart() as $cart) {
                $product_subtotal = $cart['price'] * $cart['quantity'];
                $total += $product_subtotal;
            }
            if ($total >= $coupon['min_purchase']) {
                if ($coupon['discount_type'] == 'percentage') {
                    $discount = (($total / 100) * $coupon['discount']) > $coupon['max_discount'] ? $coupon['max_discount'] : (($total / 100) * $coupon['discount']);
                } else {
                    $discount = $coupon['discount'];
                }

                session()->put('coupon_code', $request['code']);
                session()->put('coupon_discount', $discount);

                return response()->json([
                    'status' => 1,
                    'discount' => Helpers::currency_converter($discount),
                    'total' => Helpers::currency_converter($total - $discount),
                    'messages' => ['0' => 'Coupon Applied Successfully!']
                ]);
            }
        }

        return response()->json([
            'status' => 0,
            'messages' => ['0' => 'Invalid Coupon']
        ]);
    }
}
