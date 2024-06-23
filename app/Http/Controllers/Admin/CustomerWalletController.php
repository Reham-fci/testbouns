<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\BusinessSetting;
use Illuminate\Support\Facades\Validator;
use App\CPU\CustomerManager;
use Brian2694\Toastr\Facades\Toastr;
use App\CPU\Helpers;
use Illuminate\Support\Facades\Mail;
use App\Model\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Model\Notification;

class CustomerWalletController extends Controller
{
    public function add_fund_view()
    {
        $customertypes = DB::table('customertype')->get();
        $governorates = DB::table('governorates')->get();
        $cities = DB::table('cities')->get();
        if(BusinessSetting::where('type','wallet_status')->first()->value != 1)
        {
            Toastr::error(\App\CPU\translate('customer_wallet_is_disabled'));
            return back();
        } 
        return view('admin-views.customer.wallet.add_fund',compact('customertypes','governorates','cities'));
    }

    private function where($where , $condation ){
        if($where != ""){
            $where.= " and ".$condation;
        }
        else{
            $where= " where ".$condation;
        }

        return $where;
    }
    
    private function getusers($request , $notificationId){
        $condation = '';
        // order_amount >= '.$request->fromOrderprice2.'  and order_amount <= '.$request->toOrderprice2.'
        if($request->fromOrderprice2){
            $condation = $this->where($condation , 'order_amount >= "'.$request->fromOrderprice2.'"');
        }
        if($request->toOrderprice2){
            $condation = $this->where($condation , 'order_amount <= "'.$request->toOrderprice2.'"');
        }
        if($request->fromDate2){
            $condation = $this->where($condation , 'created_at >= "'.$request->fromDate2.'"');
        }
        if($request->toDate2){
            $condation = $this->where($condation , 'created_at <= "'.$request->toDate2.'"');
        }
        if($request->fromOrder2){
            $condation = $this->where($condation , 'ordercount >= "'.$request->fromOrder2.'"');
        }
        if($request->toOrder2){
            $condation = $this->where($condation , 'ordercount <= "'.$request->toOrder2.'"');
        }
        if($request->area2){
            $condation = $this->where($condation , 'area = "'.$request->area2.'"');
        }
        if($request->type2){
            $condation = $this->where($condation , 'type = "'.$request->type2.'"');
        }
        if($request->city2){
            $condation = $this->where($condation , 'city in ('.implode(',',$request->city2) . ')');
        }
        $condation = $this->where($condation , 'cm_firebase_token is not null');
        // SELECT `id`, `notificationId`, `userId` FROM `notifiUser` WHERE 1
        
        DB::insert('insert into notifiUser (notificationId, userId) 
            select  '.$notificationId.' , users.id
            from users 
            left join (
                SELECT COUNT(*) as ordercount , sum(order_amount) as order_amount , customer_id
                FROM `orders`
                GROUP by customer_id
            ) as orders on orders.customer_id = users.id 
            ' . $condation . '
        ');
        return DB::select('
            select  cm_firebase_token , users.id
            from users 
            left join (
                SELECT COUNT(*) as ordercount , sum(order_amount) as order_amount , customer_id
                FROM `orders`
                GROUP by customer_id
            ) as orders on orders.customer_id = users.id 
            ' . $condation . '
        ');
    }
    
    public function add_fund(Request $request)
    {
        ini_set('max_execution_time', 60*60*24);
        ini_set('memory_limit',-1);
        if($request->ForAll == 0){
            
            $validator = Validator::make($request->all(), [
                'amount'=>'numeric|min:.01',
            ]);
            
            // $users = $this->getusers($request);
        }
        else{
            $validator = Validator::make($request->all(), [
                'customer_id'=>'exists:users,id',
                'amount'=>'numeric|min:.01',
            ]);
            
            $users = [];
            foreach($request->customer_id as $customer_id){
                $usr =  DB::table('users')->where('id' , $customer_id)->select(DB::raw("id , cm_firebase_token"))->first();
                $users[] = $usr; 
            }
            
        }

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        
        
        $notification = new Notification;
        $notification->title = $request->notification;
        $notification->description = $request->notification;
        $notification->public = 0;
        $notification->send = 1;
        $notification->status = 1;
        $notification->save();
        
        if($request->ForAll == 0){
            
            
            
            $users = $this->getusers($request , $notification->id);
        }
        else{
            
            foreach($users as $user){
                // var_dump($user);exit;
                DB::table('notifiUser')->insert([
                        'notificationId' => $notification->id,
                        'userId' => $user->id
                    ]);
            }    
            
        }
        
        $request->expiredDate = isset($request->expiredDate) ? $request->expiredDate : null;
        $count = 0;
        foreach($users as $user){
            $wallet_transaction = CustomerManager::create_wallet_transaction($user->id, $request->amount, 'add_fund_by_admin',$request->referance , $request->expiredDate);
    
            
            if($wallet_transaction)
            {
                
                $data = [
                    'title' => "",
                    'description' => $request->notification,
                    'image' => '',
                ];
                $resultNotification = Helpers::send_push_notif_to_device($user->cm_firebase_token, $data);
                
                try{
                    Mail::to($wallet_transaction->user->email)->send(new \App\Mail\AddFundToWallet($wallet_transaction));
                }catch(\Exception $ex)
                {
                    info($ex);
                }
                $count += 1;    
            }
        }
        if($count > 0){
            return response()->json([], 200);
        }
        return response()->json(['errors'=>[
            'message'=>\App\CPU\translate('failed_to_create_transaction')
        ]], 200);  
    }

    public function report(Request $request)
    {
        $data = WalletTransaction::selectRaw('sum(credit) as total_credit, sum(debit) as total_debit')
        ->when(($request->from && $request->to),function($query)use($request){
            $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
        })
        ->when($request->transaction_type, function($query)use($request){
            $query->where('transaction_type',$request->transaction_type);
        })
        ->when($request->customer_id, function($query)use($request){
            $query->where('user_id',$request->customer_id);
        })
        ->get();
        
        $transactions = WalletTransaction::
        when(($request->from && $request->to),function($query)use($request){
            $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
        })
        ->when($request->transaction_type, function($query)use($request){
            $query->where('transaction_type',$request->transaction_type);
        })
        ->when($request->customer_id, function($query)use($request){
            $query->where('user_id',$request->customer_id);
        })
        ->latest()
        ->paginate(Helpers::pagination_limit());

        return view('admin-views.customer.wallet.report', compact('data','transactions'));
    }

}
