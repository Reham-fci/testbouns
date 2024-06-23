<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Notification;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\DB;
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $customertypes = DB::table('customertype')->get();
        $governorates = DB::table('governorates')->get();
        $cities = DB::table('cities')->get();
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $notifications = Notification::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('title', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $notifications = new Notification();
        }
        $notifications = $notifications->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.notification.index', compact('customertypes','governorates','cities','notifications','search'));
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
    public function sendCronJob()
    {
        // SELECT `id`, `title`, `description`,
        //  `image`, `status`, `created_at`, `updated_at` 
        // FROM `notifications` WHERE 1
        $notifications = DB::table('sendnotification')
        ->select(DB::raw('sendnotification.* , notifications.title , notifications.description , notifications.image , notifications.id as nid '))
        ->join('notifications' , 'notifications.id' , '=' , 'sendnotification.notifyId')
        ->where('date_notify' , '<=' , date('Y-m-d H:i'))
        ->get();
        foreach ($notifications as $request) {
            
            $notification = new Notification;
            $notification->title = $request->title;
            $notification->description = $request->description;
            $notification->image = $request->image;;

            $notification->status = 1;
            try {
                
                    DB::table('notifications')
                  ->where('id', $request->nid)
                  ->update(['send' => 1]);
                
                if($request->ForAll == 1){
    
                    Helpers::send_push_notif_to_topic($notification);
                    
                    
                }
                else{
    
                    $users = [];
                    /*
                    SELECT `id`, `name`, `f_name`, `l_name`, `phone`, `image`, `email`, `email_verified_at`,
                     `password`, `remember_token`, `created_at`, `updated_at`, `street_address`, `country`, `city`,
                      `zip`, `house_no`, `apartment_no`, `cm_firebase_token`, `is_active`, `payment_card_last_four`,
                       `payment_card_brand`, `payment_card_fawry_token`, `login_medium`, `social_id`, `is_phone_verified`, 
                       `temporary_token`, `is_email_verified`,
                     `wallet_balance`, `loyalty_point`, `type`, `area`, `getFrom` FROM `users` WHERE 1
                    
                    */ 
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
                    // if($request->toDate2){
                    //     $condation = $this->where($condation , 'ordercount <= '.$request->toOrder2);
                    // }
                    if($request->area2){
                        $condation = $this->where($condation , 'area = "'.$request->area2.'"');
                    }
                    if($request->type2){
                        $condation = $this->where($condation , 'type = "'.$request->type2.'"');
                    }
                    if($request->city2){
                        $condation = $this->where($condation , 'city in ('.$request->city2 . ')');
                    }
                    $condation = $this->where($condation , 'cm_firebase_token is not null');
                    
                    DB::insert('insert into notifiUser (notificationId, userId) 
                        select  '.$request->nid.' , users.id
                        from users 
                        left join (
                            SELECT COUNT(*) as ordercount , sum(order_amount) as order_amount , customer_id
                            FROM `orders`
                            GROUP by customer_id
                        ) as orders on orders.customer_id = users.id 
                        ' . $condation . '
                    ');
                    $_users =  DB::select('
                        select * 
                        from users 
                        left join (
                            SELECT COUNT(*) as ordercount , sum(order_amount) as order_amount , customer_id
                            FROM `orders`
                            GROUP by customer_id
                        ) as orders on orders.customer_id = users.id 
                        ' . $condation . '
                        
                    ');
                    // echo "<pre>";print_r($_users);exit;
                    foreach ($_users as $user) {
                        Helpers::send_push_notif_to_device($user->cm_firebase_token, $notification);
                    }
                }
            } catch (\Exception $e) {
                Toastr::warning('Push notification failed!');
            }

            DB::table('sendnotification')->where('id', '=', $request->id)->delete();
        }

        
    }



    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ], [
            'title.required' => 'title is required!',
        ]);

        $notification = new Notification;
        $notification->title = $request->title;
        $notification->description = $request->description;

        if ($request->has('image')) {
            $notification->image = ImageManager::upload('notification/', 'png', $request->file('image'));
        } else {
            $notification->image = 'null';
        }

        $notification->public = $request->ForAll;
        $notification->send = 0;
        $notification->status = 1;
        $notification->save();
        $nId = $notification->id;
        try {
            
            if(date('Y-m-d H:i:s') < date('Y-m-d H:i:s' , strtotime($request->date_notify))){
                DB::table('sendnotification')->insert([
                    'fromOrderprice2' => $request->fromOrderprice2,
                    'toOrderprice2' => $request->toOrderprice2,
                    'fromDate2' => $request->fromDate2,
                    'toDate2' => $request->toDate2,
                    'fromOrder2' => $request->fromOrder2,
                    'toOrder2' => $request->toOrder2,
                    'area2' => $request->area2,
                    'type2' => $request->type2,
                    'ForAll' => $request->ForAll,
                    'notifyId' => $notification->id,
                    'date_notify' =>  date('Y-m-d H:i:s' , strtotime($request->date_notify)),
                    'city2' => implode(',',$request->city2)
                ]);
            }
            else if($request->ForAll == 1){

                    DB::table('notifications')
                  ->where('id', $nId)
                  ->update(['send' => 1]);
                Helpers::send_push_notif_to_topic($notification);
                
            }
            else{
                
                
                DB::table('notifications')
              ->where('id',$nId)
              ->update(['send' => 1]);
                $users = [];
                /*
                SELECT `id`, `name`, `f_name`, `l_name`, `phone`, `image`, `email`, `email_verified_at`,
                 `password`, `remember_token`, `created_at`, `updated_at`, `street_address`, `country`, `city`,
                  `zip`, `house_no`, `apartment_no`, `cm_firebase_token`, `is_active`, `payment_card_last_four`,
                   `payment_card_brand`, `payment_card_fawry_token`, `login_medium`, `social_id`, `is_phone_verified`, 
                   `temporary_token`, `is_email_verified`,
                 `wallet_balance`, `loyalty_point`, `type`, `area`, `getFrom` FROM `users` WHERE 1
                
                */ 
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
                // if($request->toDate2){
                //     $condation = $this->where($condation , 'ordercount <= '.$request->toOrder2);
                // }
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
                    select  '.$nId.' , users.id
                    from users 
                    left join (
                        SELECT COUNT(*) as ordercount , sum(order_amount) as order_amount , customer_id
                        FROM `orders`
                        GROUP by customer_id
                    ) as orders on orders.customer_id = users.id 
                    ' . $condation . '
                ');
                $_users =  DB::select('
                    select * 
                    from users 
                    left join (
                        SELECT COUNT(*) as ordercount , sum(order_amount) as order_amount , customer_id
                        FROM `orders`
                        GROUP by customer_id
                    ) as orders on orders.customer_id = users.id 
                    ' . $condation . '
                    
                ');
                foreach ($_users as $user) {
                    Helpers::send_push_notif_to_device($user->cm_firebase_token, $notification);
                }
                
            }
        } catch (\Exception $e) {
            Toastr::warning('Push notification failed!');
        }
        
        Toastr::success('Notification sent successfully!');
        return back();
    }

    public function edit($id)
    {
        $notification = Notification::find($id);
        return view('admin-views.notification.edit', compact('notification'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ], [
            'title.required' => 'title is required!',
        ]);

        $notification = Notification::find($id);
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->image = $request->has('image')? ImageManager::update('notification/', $notification->image, 'png', $request->file('image')):$notification->image;
        $notification->save();

        Toastr::success('Notification updated successfully!');
        return back();
    }

    public function status(Request $request)
    {
        if ($request->ajax()) {
            $notification = Notification::find($request->id);
            $notification->status = $request->status;
            $notification->save();
            $data = $request->status;
            return response()->json($data);
        }
    }

    public function delete(Request $request)
    {
        $notification = Notification::find($request->id);
        ImageManager::delete('/notification/' . $notification['image']);
        $notification->delete();
        return response()->json();
    }
}
