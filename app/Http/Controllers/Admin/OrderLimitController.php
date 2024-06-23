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
use stdClass;

class OrderLimitController extends Controller
{
    public function List(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        
        $orderLimit = DB::table('orderLimit')
        ->select(DB::raw('orderLimit.id, orderLimit.created_at, orderLimit.Limit , GROUP_CONCAT(city_name_ar) as cities'))
        ->join('orderLimitArea' , 'orderLimitArea.OrderLimitID' , '=' , 'orderLimit.id')
        ->join('cities' , 'orderLimitArea.areaId' , '=' , 'cities.id')
        ->groupBy('orderLimit.id');

        
        
        // echo $cou->toSql();exit();
        $orderLimit = $orderLimit->latest()->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.OrderLimit.list', compact('orderLimit' , 'search'));
    }

    public function manage($id = 0){
        $areas = [];
        $orderLimit = new stdClass();
        if($id != 0){
            $orderLimit = DB::table('orderLimit')->where('id' , $id)->get()[0];
            $_areas = DB::table('orderLimitArea')->where('OrderLimitID' , $id)->get();
            foreach ($_areas as $area) {
                $areas[] =   $area->areaId;
            }           
        }
        $governorates = DB::table('governorates')->get();
        foreach ($governorates as &$governorate) {
            $governorate->areas = DB::table('cities')->where('governorate_id' ,$governorate->id )->get();
        }
        return view('admin-views.OrderLimit.manage', compact('id','areas','orderLimit','governorates'));
    }

    

    public function insertandUpdate(Request $request)
    {

        $request->validate([
            'limit' => 'required',
        ]);
        $id = isset($request->id) ? $request->id : 0;
        if($id){
            DB::table('orderLimit')->where('id',$id)->update(
                array('limit' => $request->limit)
            );
            
            DB::table('orderLimitArea')->where('OrderLimitID',$id)->delete();
        }
        else{

            $id = DB::table('orderLimit')->insertGetId(
                array('limit' => $request->limit)
            );
        }
        foreach ($request['area'] as $areaId) {
            DB::table('orderLimitArea')->insertGetId(
                array(
                    'areaId' => $areaId ,
                    'OrderLimitID' => $id
                )
            );
        }
                
        
        Toastr::success('orderLimit added successfully!');
        return back();
    }

    
    public function delete($id)
    {
        DB::table('orderLimit')->where('id',$id)->delete();
        DB::table('orderLimitArea')->where('OrderLimitID',$id)->delete();
        Toastr::success('Coupon deleted successfully!');
        return back();
    }
}
