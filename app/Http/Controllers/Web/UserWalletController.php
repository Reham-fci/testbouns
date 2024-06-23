<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WalletTransaction;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use App\CPU\CustomerManager;
use Illuminate\Support\Facades\DB;
class UserWalletController extends Controller
{
    public function index()
    {
        $wallet_status = Helpers::get_business_settings('wallet_status');

        if($wallet_status == 1)
        {
            $total_wallet_balance = auth('customer')->user()->wallet_balance;
        $wallet_transactio_list = WalletTransaction::where('user_id',auth('customer')->id())
                                                    ->latest()
                                                    ->paginate(15);
        return view('web-views.users-profile.user-wallet',compact('total_wallet_balance','wallet_transactio_list'));
        }else{
            Toastr::warning(\App\CPU\translate('access_denied!'));
            return back();
        }
    }
    
    
    
    function remove_expired_fund_from_wallet(){
        
            $WalletTransactionRows = WalletTransaction::whereIn('transaction_type' , ["add_fund_by_admin"])
            ->where("expiredDate" ,"<" , date('Y-m-d'))
            ->where("balance" , ">" , 0)
            ->where("done" , "=" , 0)
            ->get();
            // WalletTransaction::whereIn('transaction_type' , ["add_fund_by_admin"])
            // ->where("expiredDate" ,"<" , date('Y-m-d'))
            // ->where("balance" , ">" , 0)
            // ->where("done" , "=" , 0)
            // ->update([
            //         "done" => 1
            //     ]);
            
            
            
                // dd($WalletTransactionRows);
            $ids = [];
            foreach($WalletTransactionRows as $row){
                $ids[] = $row->id;
                // $wallet_transaction = CustomerManager::create_wallet_transaction($row->user_id,$row->balance,'expired_fund','expired_fund');
            }
            
            WalletTransaction::whereIn('transaction_type' , $ids)
            ->update([
                "done" => 1
            ]);
            DB::update("
            
                UPDATE users JOIN (
            	SELECT sum(`balance`) b , user_id FROM `wallet_transactions` WHERE `done` = 0 and `transaction_type` in ('loyalty_point','add_fund_by_admin','return_money_order')
            	GROUP by user_id
                ) as wallet
                on wallet.user_id = users.id
                set users.wallet_balance = wallet.b
            ");
            
            
            
            
    }
}
