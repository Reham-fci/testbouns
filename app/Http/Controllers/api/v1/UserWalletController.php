<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WalletTransaction;
use App\CPU\Helpers;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use App\Model\BusinessSetting;
use Illuminate\Support\Facades\DB;
 
class UserWalletController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $wallet_status = Helpers::get_business_settings('wallet_status');

        if($wallet_status == 1)
        {
            $user = $request->user();
            $total_wallet_balance = $user->wallet_balance;
            $wallet_transactio_list = WalletTransaction::where('user_id',$user->id)
                                                    ->latest()
                                                    ->paginate($request['limit'], ['*'], 'page', $request['offset']);
        
            return response()->json([
                'limit'=>(integer)$request->limit,
                'offset'=>(integer)$request->offset,
                'total_wallet_balance'=>$total_wallet_balance,
                'total_wallet_transactio'=>$wallet_transactio_list->total(),
                'wallet_transactio_list'=>$wallet_transactio_list->items()
            ],200);
            
        }else{
            
            return response()->json(['message' => translate('access_denied!')], 422);
        }
    }
    public function debit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $wallet_status = Helpers::get_business_settings('wallet_status');

        if($wallet_status == 1)
        {
            $user = $request->user();
            $total_wallet_balance = $user->wallet_balance;
            $wallet_transactio_list = WalletTransaction::
                                                    select(DB::raw("wallet_transactions.* , 
                                                        if(
                                                            transaction_type = 'make_order' , '".translate('make_order')."' ,
                                                            if(transaction_type = 'remove_loyalty_point' , '".translate('remove_loyalty_point')."'
                                                            , '".translate('expired_fund')."'
                                                            ) 
                                                        )as transaction_type
                                                    "))
                                                    ->where('user_id',$user->id)
                                                    ->whereIn("transaction_type",["make_order" , "remove_loyalty_point" , "expired_fund"])
                                                    ->latest()
                                                    ->paginate($request['limit'], ['*'], 'page', $request['offset']);
        
            return response()->json([
                'limit'=>(integer)$request->limit,
                'offset'=>(integer)$request->offset,
                'wallet_balance'=>$user->wallet_balance,
                'total_wallet_balance'=>$total_wallet_balance,
                'total_wallet_transactio'=>$wallet_transactio_list->total(),
                'wallet_transactio_list'=>$wallet_transactio_list->items()
            ],200);
            
        }else{
            
            return response()->json(['message' => translate('access_denied!')], 422);
        }
    }
    
    public function credit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $wallet_status = Helpers::get_business_settings('wallet_status');

        if($wallet_status == 1)
        {
            $user = $request->user();
            $total_wallet_balance = $user->wallet_balance;
            $wallet_transactio_list = WalletTransaction::
                                                    select(DB::raw("wallet_transactions.* , 
                                                        if(
                                                            transaction_type = 'loyalty_point' , '".translate('loyalty_point')."' ,
                                                            if(transaction_type = 'add_fund_by_admin' , '".translate('add_fund_by_admin')."'
                                                            , '".translate('return_money_order')."'
                                                            ) 
                                                        )as transaction_type
                                                    "))
                                                    ->where('user_id',$user->id)
                                                    ->whereIn("transaction_type",["loyalty_point","add_fund_by_admin","return_money_order"])
                                                    ->latest()
                                                    ->paginate($request['limit'], ['*'], 'page', $request['offset']);
        
            return response()->json([
                'limit'=>(integer)$request->limit,
                'offset'=>(integer)$request->offset,
                'wallet_balance'=>$user->wallet_balance,
                'total_wallet_balance'=>$total_wallet_balance,
                'total_wallet_transactio'=>$wallet_transactio_list->total(),
                'wallet_transactio_list'=>$wallet_transactio_list->items()
            ],200);
            
        }else{
            
            return response()->json(['message' => translate('access_denied!')], 422);
        }
    }
    
    public function setting(Request $request)
    {
            $settings = array_column(BusinessSetting::whereIn('type',['walletOrderWithRate','walletOrderValue'])->get()->toArray(), 'value','type');


            
            $user = $request->user();
        
            return response()->json([
                'wallet_balance'=>$user->wallet_balance,
                'settings'=>$settings,
            ],200);
            
        
        
    
        
    }
}
