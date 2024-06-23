<?php

namespace App\Http\Controllers\api\v1;

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

        try {
            $couponLimit = Order::where('customer_id', $request->user()->id)
                ->where('coupon_code', $request['code'])->count();

            $coupon = Coupon::where(['code' => $request['code']])
                ->where('limit', '>', $couponLimit)
                ->where('status', '=', 1)
                ->whereDate('start_date', '<=', Carbon::parse()->toDateString())
                ->whereDate('expire_date', '>=', Carbon::parse()->toDateString())->first();
            //$coupon = Coupon::where(['code' => $request['code']])->first();
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }

        return response()->json($coupon, 200);
    }


    
    public function apply2(Request $request)
    {
        
        $total = $request['total'];
        $qty = $request['qty'];
        $user = DB::table('users')->where('id' , $request->user()->id)->first();
        $shipping_addresses = DB::table('shipping_addresses')->where('id' , $request['address_id'])->first();
        $order = DB::table('orders')->select(DB::raw('count(*) as order_count'))
        ->where('customer_id' , $request->user()->id)
        ->where('order_status' , 'delivered')->first();
      
        
        
        
        
        
        $couponLimit = Order::where('customer_id', $request->user()->id)
            ->where('coupon_code', $request['code'])->count();
        
        
            
        try {
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
            
            
            $coupon = DB::select($sql);
            if ($coupon) {
                $coupon = $coupon[0];
                $coupon->limit = $coupon->limit ."";
                $coupon->id = $coupon->id ."";
                $coupon->status = $coupon->status ."";
                $coupon->forall = $coupon->forall ."";
            }
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }

        
        return response()->json([$coupon], 200);
    }
}
