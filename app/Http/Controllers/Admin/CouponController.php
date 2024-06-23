<?php

namespace App\Http\Controllers\Admin;

use App\CPU\BackEndHelper;
use App\CPU\Convert;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function add_new(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        
        $seaerchData['fromDate'] = isset($request['fromDate']) ? $request['fromDate'] : "";
        $seaerchData['toDate'] = isset($request['toDate']) ? $request['toDate'] : "";
        $seaerchData['fromOrder'] = isset($request['fromOrder']) ? $request['fromOrder'] : "";
        $seaerchData['fromOrderprice'] = isset($request['fromOrderprice']) ? $request['fromOrderprice'] : "";
        $seaerchData['toOrder'] = isset($request['toOrder']) ? $request['toOrder'] : "";
        $seaerchData['toOrderprice'] = isset($request['toOrderprice']) ? $request['toOrderprice'] : "";
        $seaerchData['city'] = isset($request['city']) ? $request['city'] : "";
        $seaerchData['area'] = isset($request['area']) ? $request['area'] : "";
        $seaerchData['type'] = isset($request['type']) ? $request['type'] : "";
        $customertypes = DB::table('customertype')->get();
        $governorates = DB::table('governorates')->get();
        $cities = DB::table('cities')->get();

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $cou = Coupon::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%")
                        ->orWhere('code', 'like', "%{$value}%")
                        ->orWhere('discount_type', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $cou = new Coupon();
        }

        
        if ($request['fromDate']) {
            
            $query_param['fromDate'] =  $request['fromDate'];
            // echo $request['fromDate'];exit();
            // $cou->where('FromRegisterDate' , '>=' ,$request['fromDate'] );
            $cou = $cou->where(function ($q)  use ($request) {
                $q->where('FromRegisterDate', '>=', $request['fromDate'] )
                      ->orWhere('ToRegisterDate', '<=', $request['fromDate'] );
            });
            
        }
        if ($request['toDate']) {
            
            $query_param['toDate'] =  $request['toDate'];
            $cou = $cou->where(function ($q) use ($request) {
                $q->where('FromRegisterDate', '>=', $request['toDate'] )
                      ->orWhere('ToRegisterDate', '<=', $request['toDate'] );
            });
        }
        if ($request['fromOrder']) {
            
            $query_param['fromOrder'] =  $request['fromOrder'];
            $cou->where('FromOrderTimes' , '>=' ,$request['fromOrder'] );
        }
        if ($request['toOrder']) {
            
            $query_param['toOrder'] =  $request['toOrder'];
            $cou->where('ToOrderTimes', '<=' ,$request['toOrder'] );
        }
        if ($request['fromOrderprice']) {
            
            $query_param['fromOrderprice'] =  $request['fromOrderprice'];
            $cou->where('Fromprice' , '>=' ,$request['fromOrderprice'] );
        }
        if ($request['toOrderprice']) {
            
            $query_param['toOrderprice'] =  $request['toOrderprice'];
            $cou->where('Toprice' , '<=' ,$request['toOrderprice'] );
        }
        if ($request['city']) {
            
            $query_param['city'] =  $request['city'];
            $cou->where('city' , '=' ,$request['city'] );
        }
        if ($request['area']) {
            
            $query_param['area'] =  $request['area'];
            $cou->where('area' , '=' ,$request['area'] );
        }
        if ($request['type']) {
            
            $query_param['type'] =  $request['type'];
            
            $cou->where('type' , '=' ,$request['type'] );
        }
        // echo $cou->toSql();exit();
        $cou = $cou->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.coupon.add-new', compact('cou','customertypes','governorates','cities','seaerchData', 'search'));
    }

    public function export(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        
        $seaerchData['fromDate'] = isset($request['fromDate']) ? $request['fromDate'] : "";
        $seaerchData['toDate'] = isset($request['toDate']) ? $request['toDate'] : "";
        $seaerchData['fromOrder'] = isset($request['fromOrder']) ? $request['fromOrder'] : "";
        $seaerchData['fromOrderprice'] = isset($request['fromOrderprice']) ? $request['fromOrderprice'] : "";
        $seaerchData['toOrder'] = isset($request['toOrder']) ? $request['toOrder'] : "";
        $seaerchData['toOrderprice'] = isset($request['toOrderprice']) ? $request['toOrderprice'] : "";
        $seaerchData['city'] = isset($request['city']) ? $request['city'] : "";
        $seaerchData['area'] = isset($request['area']) ? $request['area'] : "";
        $seaerchData['type'] = isset($request['type']) ? $request['type'] : "";
        $customertypes = DB::table('customertype')->get();
        $governorates = DB::table('governorates')->get();
        $cities = DB::table('cities')->get();

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $cou = Coupon::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%")
                        ->orWhere('code', 'like', "%{$value}%")
                        ->orWhere('discount_type', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $cou = new Coupon();
        }

        
        if ($request['fromDate']) {
            
            $query_param['fromDate'] =  $request['fromDate'];
            // echo $request['fromDate'];exit();
            $cou->where(function ($query)  use ($request) {
                $query->where('FromRegisterDate', '>=', $request['fromDate'] )
                      ->orWhere('ToRegisterDate', '<=', $request['fromDate'] );
            });
        }
        if ($request['toDate']) {
            
            $query_param['toDate'] =  $request['toDate'];
            $cou->where(function ($query) use ($request) {
                $query->where('FromRegisterDate', '>=', $request['toDate'] )
                      ->orWhere('ToRegisterDate', '<=', $request['toDate'] );
            });
        }
        if ($request['fromOrder']) {
            
            $query_param['fromOrder'] =  $request['fromOrder'];
            $cou->where('FromOrderTimes' , '>=' ,$request['fromOrder'] );
        }
        if ($request['toOrder']) {
            
            $query_param['toOrder'] =  $request['toOrder'];
            $cou->where('ToOrderTimes', '<=' ,$request['toOrder'] );
        }
        if ($request['fromOrderprice']) {
            
            $query_param['fromOrderprice'] =  $request['fromOrderprice'];
            $cou->where('Fromprice' , '>=' ,$request['fromOrderprice'] );
        }
        if ($request['toOrderprice']) {
            
            $query_param['toOrderprice'] =  $request['toOrderprice'];
            $cou->where('Toprice' , '<=' ,$request['toOrderprice'] );
        }
        if ($request['city']) {
            
            $query_param['city'] =  $request['city'];
            $cou->where('city' , '=' ,$request['city'] );
        }
        if ($request['area']) {
            
            $query_param['area'] =  $request['area'];
            $cou->where('area' , '=' ,$request['area'] );
        }
        if ($request['type']) {
            
            $query_param['type'] =  $request['type'];
            
            $cou->where('type' , '=' ,$request['type'] );
        }

        $cou = $cou->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.coupon.add-new', compact('cou', 'search'));
    }

    public function store(Request $request)
    {
        // echo $request->forall."<br>";exit();
        $request->validate([
            'code' => 'required',
            'title' => 'required',
            'start_date' => 'required',
            'expire_date' => 'required',
            'discount' => 'required',
            'min_purchase' => 'required',
            'limit' => 'required',
        ]);
        $request->city2 = isset($request->city2) ? $request->city2 : [];
        $request->area2 = isset($request->area2) ? $request->area2 : [];

        $coupon = new Coupon();
        $coupon->coupon_type = $request->coupon_type;
        $coupon->title = $request->title;
        $coupon->code = $request->code;
        $coupon->start_date = $request->start_date;
        $coupon->expire_date = $request->expire_date;
        $coupon->min_purchase = Convert::usd($request->min_purchase);
        $coupon->max_discount = Convert::usd($request->max_discount != null ? $request->max_discount : $request->discount);
        $coupon->discount = $request->discount_type == 'amount' ? Convert::usd($request->discount) : $request['discount'];
        $coupon->discount_type = $request->discount_type;
        $coupon->limit = $request->limit;
        $coupon->status = 1;
        $coupon->qty = $request->qty;
        if($request->forall == 0){

            $coupon->forall = 0;
            $coupon->FromRegisterDate = $request->fromDate2;
            $coupon->ToRegisterDate = $request->toDate2;
            $coupon->FromOrderTimes = $request->fromOrder2;
            $coupon->ToOrderTimes = $request->toOrder2;
            $coupon->Fromprice = $request->fromOrderprice2;
            $coupon->Toprice = $request->toOrderprice2;
            $coupon->city = implode(',' , $request->city2);
            $coupon->area = implode(',' , $request->area2);
            $coupon->type = $request->type2;
        }
        else{



            
            $coupon->forall = 1;
            $coupon->FromOrderTimes = 0;
            $coupon->ToOrderTimes = 0;
            $coupon->Fromprice = 0;
            $coupon->Toprice = 0;
            $coupon->city = "";
            $coupon->area = "";
            $coupon->type = "";
        }
        
        
        $coupon->save();

        
        Toastr::success('Coupon added successfully!');
        return back();
    }

    public function edit($id)
    {
        
        $customertypes = DB::table('customertype')->get();
        $governorates = DB::table('governorates')->get();
        $cities = DB::table('cities')->get();
        $c = Coupon::where(['id' => $id])->first();
        $c['cityList'] = explode(',',$c['city']);
        $c['areaList'] = explode(',',$c['area']);
        return view('admin-views.coupon.edit', compact('c','customertypes','governorates','cities'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required',
            'title' => 'required',
            'start_date' => 'required',
            'expire_date' => 'required',
            'discount' => 'required',
            'min_purchase' => 'required',
            'limit' => 'required',
        ]);

        $request->city2 = isset($request->city2) ? $request->city2 : [];
        $request->area2 = isset($request->area2) ? $request->area2 : [];
        
        if($request->forAll == 0){
            $forAll = 0;
            $FromRegisterDate = $request->fromDate2;
            $ToRegisterDate = $request->toDate2;
            $FromOrderTimes = $request->fromOrder2;
            $ToOrderTimes = $request->toOrder2;
            $Fromprice = $request->fromOrderprice2;
            $Toprice = $request->toOrderprice2;
            $city = implode(',' , $request->city2);
            $area = implode(',' , $request->area2);
            $type = $request->type2;
        }
        else{

            $forAll = 1;
            $FromRegisterDate = NULL;
            $ToRegisterDate = NULL;
            $FromOrderTimes = 0;
            $ToOrderTimes = 0;
            $Fromprice = 0;
            $Toprice = 0;
            $city = 0;
            $area = 0;
            $type = 0;
        }
        DB::table('coupons')->where(['id' => $id])->update([
            'coupon_type' => $request->coupon_type,
            'title' => $request->title,
            'code' => $request->code,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'min_purchase' => Convert::usd($request->min_purchase),
            'max_discount' => Convert::usd($request->max_discount != null ? $request->max_discount : $request->discount),
            'discount' => $request->discount_type == 'amount' ? Convert::usd($request->discount) : $request['discount'],
            'discount_type' => $request->discount_type,
            'updated_at' => now(),
            'limit' => $request->limit,
            'qty' => $request->qty,
            'forall' => $forAll,
            'FromRegisterDate' => $FromRegisterDate,
            'ToRegisterDate' => $ToRegisterDate,
            'FromOrderTimes' => $FromOrderTimes,
            'ToOrderTimes' => $ToOrderTimes,
            'Fromprice' => $Fromprice,
            'Toprice' => $Toprice,
            'city' => $city,
            'area' => $area,
            'type' => $type
        ]);

        Toastr::success('Coupon updated successfully!');
        return back();
    }

    public function status(Request $request)
    {
        $coupon = Coupon::find($request->id);
        $coupon->status = $request->status;
        $coupon->save();
        // $data = $request->status;
        // return response()->json($data);
        Toastr::success('Coupon status updated!');
        return back();
    }

    public function delete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        Toastr::success('Coupon deleted successfully!');
        return back();
    }
}
