<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Model\Notification;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class NotificationController extends Controller
{
    public function get_notifications_old()
    {
        try {
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_notifications(Request $request)
    {
        // $request->userId = isset($request->userId) ? $request->userId : 0;
        $userId= isset($request->userId) ? $request->userId : 0;
        try {
            if($userId){
                
                $notifications = DB::select('
                select notifications.* from notifications
                left join notifiUser on notifiUser.notificationId = notifications.id
                where (notifications.public = 1 or userId = '.$userId.') 
                and send=1  order by notifications.created_at desc limit 15' );
                foreach($notifications as $notification){
                    $notification->id = "".$notification->id;
                    $notification->public = "".$notification->public;
                    $notification->send = "".$notification->send;
                    $notification->status = "".$notification->status;
                }
                return response()->json($notifications, 200);
            }
            else{
                
            return response()->json(Notification::active()->orderBy('id','DESC')->get(), 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}
