<?php

namespace App\Http\Controllers\Customer\Auth;

use App\CPU\Helpers;
use App\CPU\SMS_module;
use App\Http\Controllers\Controller;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function App\CPU\translate;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:customer', ['except' => ['logout']]);
    }

    public function reset_password()
    {
        
        return view('customer-view.auth.recover-password');
    }

    

    public function reset_password_ajax(Request $request)
    {
        $request->validate([
            'identity' => 'required',
        ]);
        $identity = $request['identity'];
        
        $recaptchaToken = $request['recaptchaToken'];
        // $token = rand(1000, 9999);
        echo json_encode(SMS_module::send($identity, $recaptchaToken));
    }
    public function reset_password2(Request $request)
    {
        $request->validate([
            'identity' => 'required',
        ]);
        $identity = $request['identity'];
        $recaptchaToken = $request['recaptchaToken'];
        // echo "recaptchaToken";
        // echo "<pre>";print_r($recaptchaToken);;
        // echo "<pre>";print_r($_POST);exit();
        // $confirmationResult = $request['confirmationResult'];
        $customer = User::where('phone', 'like', "%{$request['identity']}%")->first();
        if (isset($customer) && $recaptchaToken) {
            session()->put('forgot_password_identity', $customer['id']);
            $token = rand(1000, 9999);
            DB::table('password_resets')->insert([
                
                'identity' => ltrim(str_replace("+2","",$customer['phone']),"2"),
                'token' => $token,
                'user_type'=>'customer',
                'created_at' => now(),
            ]);
            $res = SMS_module::send($customer->phone, $recaptchaToken);
            if(isset($res->sessionInfo)){

                $sessionInfo  = $res->sessionInfo;
            }
            else{
                $sessionInfo  = "";
            }
            Toastr::success('Check your phone. Password reset otp sent.');
            return view('customer-view.auth.verify-otp',compact('identity' ,'recaptchaToken', 'sessionInfo'));
        }
    }

    public function reset_password_request(Request $request)
    {
        $request->validate([
            'identity' => 'required',
        ]);
        $identity = $request['identity'];
        session()->put('forgot_password_identity', $request['identity']);
        $verification_by = Helpers::get_business_settings('forgot_password_verification');

        DB::table('password_resets')->where('user_type','customer')->where('identity', 'like', "%{$request['identity']}%")->delete();

        if ($verification_by == 'email') {
            $customer = User::Where(['email' => $request['identity']])->first();
            if (isset($customer)) {
                $token = Str::random(120);
                DB::table('password_resets')->insert([
                    'identity' => $customer['email'],
                    'token' => $token,
                    'user_type'=>'customer',
                    'created_at' => now(),
                ]);
                $reset_url = url('/') . '/customer/auth/reset-password?token=' . $token;
                Mail::to($customer['email'])->send(new \App\Mail\PasswordResetMail($reset_url));
                Toastr::success('Check your email. Password reset url sent.');
                return back();
            }
        } elseif ($verification_by == 'phone') {
            $customer = User::where('phone', 'like', "%{$request['identity']}%")->first();
            if (isset($customer)) {
                $token = rand(1000, 9999);
                DB::table('password_resets')->insert([
                    'identity' => $customer['phone'],
                    'token' => $token,
                    'user_type'=>'customer',
                    'created_at' => now(),
                ]);
                SMS_module::send($customer->phone, $token);
                Toastr::success('Check your phone. Password reset otp sent.');
                return view('customer-view.auth.recover-password');
                // return redirect()->route('customer.auth.otp-verification');
            }
        }

        Toastr::error('No such user found!');
        return back();
    }

    public function otp_verification()
    {
        return view('customer-view.auth.verify-otp');
    }

    public function otp_verification_submit(Request $request)
    {
        $id = session('forgot_password_identity');
        $data = DB::table('password_resets')->where('user_type','customer')->where(['token' => $request['otp']])
            ->where('identity', 'like', "%{$id}%")
            ->first();
        if (isset($data)) {
            $token = $request['otp'];
            return redirect()->route('customer.auth.reset-password', ['token' => $token]);
        }

        Toastr::error(translate('invalid_otp'));
        return back();
    }

    public function otp_verification_submit2(Request $request)
    {
        $id = session('forgot_password_identity');
        $res = SMS_module::verifyPhoneNumber( $request['otp'] , $request['phone']);
        $identity = $request['phone'];
        $sessionInfo = $request['sessionInfo'];
        $recaptchaToken = $request['recaptchaToken'];
        if(!$res){
            // print_r($res);
            Toastr::error(translate('invalid_otp'));
            return view('customer-view.auth.verify-otp',compact('identity' ,'recaptchaToken', 'sessionInfo'));
        }
        else{
            return view('customer-view.auth.reset-password', compact('id'));
        }
        
    }

    public function reset_password_index(Request $request)
    {
        $data = DB::table('password_resets')->where('user_type','customer')->where(['token' => $request['token']])->first();
        if (isset($data)) {
            $token = $request['token'];
            return view('customer-view.auth.reset-password', compact('token'));
        }
        Toastr::error('Invalid credentials');
        return back();
    }

    public function reset_password_submit2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|same:confirm_password',
        ]);

        $token = $request['reset_token'];
        if ($validator->fails()) {
            Toastr::error(translate('password_mismatch'));
            return view('customer-view.auth.reset-password', compact('token'));
        }

        $id = session('forgot_password_identity');
        $data = User::Where('id', '=', $request['reset_token'])->first();

        if (isset($data)) {
            User::Where('id', '=', $request['reset_token'])
                ->update([
                    'password' => bcrypt(str_replace(' ', '', $request['password']))
                ]);
            Toastr::success('Password reset successfully.');
            DB::table('password_resets')->where('user_type','customer')->where(['token' => $request['reset_token']])->delete();
            return redirect('/');
        }
        Toastr::error('Invalid data.');
        return back();
    }
    public function reset_password_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|same:confirm_password',
        ]);

        $token = $request['reset_token'];
        if ($validator->fails()) {
            Toastr::error(translate('password_mismatch'));
            return view('customer-view.auth.reset-password', compact('token'));
        }

        $id = session('forgot_password_identity');
        $data = DB::table('password_resets')
            ->where('user_type','=','"customer"')
            ->where(['id' => $request['reset_token']])->first();

        if (isset($data)) {
            User::Where('id', '=', $request['reset_token'])
                ->update([
                    'password' => bcrypt(str_replace(' ', '', $request['password']))
                ]);
            Toastr::success('Password reset successfully.');
            DB::table('password_resets')->where('user_type','customer')->where(['token' => $request['reset_token']])->delete();
            return redirect('/');
        }
        Toastr::error('Invalid data.');
        return back();
    }
}
