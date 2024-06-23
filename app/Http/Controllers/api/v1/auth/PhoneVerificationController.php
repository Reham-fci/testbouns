<?php

namespace App\Http\Controllers\api\v1\auth;

use App\CPU\Helpers;
use App\CPU\SMS_module;
use App\Http\Controllers\Controller;
use App\Model\PhoneOrEmailVerification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function App\CPU\translate;

class PhoneVerificationController extends Controller
{
    public function check_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temporary_token' => 'required',
            'phone' => 'required|min:11|max:14'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = User::where(['temporary_token' => $request->temporary_token])->first();

        if (isset($user) == false) {
            return response()->json([
                'message' => translate('temporary_token_mismatch'),
            ], 200);
        }

        $token = rand(1000, 9999);
        DB::table('phone_or_email_verifications')->insert([
            'phone_or_email' => $request['phone'],
            'token' => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $response = SMS_module::send($request['phone'], $token);
        return response()->json([
            'message' => $response,
            'token' => 'active'
        ], 200);
    }

    public function verify_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'temporary_token' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $verify = PhoneOrEmailVerification::where(['phone_or_email' => $request['phone'], 'token' => $request['otp']])->first();

        if (isset($verify)) {
            try {
                $user = User::where(['temporary_token' => $request['temporary_token']])->first();
                $user->phone = $request['phone'];
                $user->is_phone_verified = 1;
                $user->save();
                $verify->delete();
            } catch (\Exception $exception) {
                return response()->json([
                    'message' => translate('temporary_token_mismatch'),
                ], 200);
            }

            $token = $user->createToken('LaravelAuthApp')->accessToken;
            return response()->json([
                'message' => translate('otp_verified'),
                'token' => $token
            ], 200);
        }

        return response()->json(['errors' => [
            ['code' => 'token', 'message' => translate('otp_not_found')]
        ]], 404);
    }
    
    
    
    public function forgetPassword(Request $request)
    {
        $request->validate([
            'identity' => 'required',
        ]);
        $identity = $request['identity'];
        $customer = User::where('phone', 'like', "%{$request['identity']}%")->first();
        if (isset($customer) ) {
            session()->put('forgot_password_identity', $customer['id']);
            $token = rand(1000, 9999);
            DB::table('password_resets')->insert([
                
                'identity' => ltrim(str_replace("+2","",$customer['phone']),"2"),
                'token' => $token,
                'user_type'=>'customer',
                'created_at' => now(),
            ]);
            $customer['phone']  = substr($customer['phone'] , -11 , 11); 
            $res = SMS_module::send( ltrim(str_replace("+2","",$customer['phone']),"2"), '');
            
            
            
            return response()->json([
                'message' => 'code send',
                'status' => 1,
                'res' => $res
            ], 200);
        }

        
        return response()->json(['errors' => "no User"  , "status" => 0], 404);
    }

    public function changepassword(Request $request)
    {
         $request['phone'] = ltrim(str_replace("+2","",$request['phone']),"2");
        $res = SMS_module::verifyPhoneNumber( $request['otp'] , $request['phone']);
        
        $customer = User::where('phone', 'like', "%{$request['phone']}%")->first();
        $customer->password = bcrypt($request['password']);
        $customer->save();
        if($res){
            return response()->json([
                'message' => 'password change',
                'res' => $res ,
                'status' => 1
            ], 200);
        }
        else{
            return response()->json(['res' => $res , 'errors' => "code error" , "status" => 0], 404);
        }
        
    }
    
    
    
}
