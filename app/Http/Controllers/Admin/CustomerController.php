<?php

namespace App\Http\Controllers\Admin;
require __DIR__ .'/SimpleXLSXGen.php';
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Subscription;
use App\Model\BusinessSetting;
use App\Model\Customer;
use App\Model\Salesperson;
use Faker\Provider\ar_EG\Address;
use SimpleXLSXGen;
class CustomerController extends Controller
{
    public function customer_list(Request $request)
    {

        User::where(['user_checked' => 0])->update(['user_checked' => 1]);
        $query_param = [];
        $search = $request['search'];
        $seaerchData['fromDate'] = isset($request['fromDate']) ? $request['fromDate'] : "";
        $seaerchData['toDate'] = isset($request['toDate']) ? $request['toDate'] : "";
        $seaerchData['fromOrder'] = isset($request['fromOrder']) ? $request['fromOrder'] : "";
        $seaerchData['fromOrderprice'] = isset($request['fromOrderprice']) ? $request['fromOrderprice'] : "";
        $seaerchData['toOrder'] = isset($request['toOrder']) ? $request['toOrder'] : "";
        $seaerchData['toOrderprice'] = isset($request['toOrderprice']) ? $request['toOrderprice'] : "";
        $seaerchData['city'] = isset($request['city']) ? $request['city'] : [];
        $seaerchData['area'] = isset($request['area']) ? $request['area'] : "";
        $seaerchData['type'] = isset($request['type']) ? $request['type'] : "";
        $seaerchData['salesPersonId'] = isset($request['salesPersonId']) ? $request['salesPersonId'] : 0;
        $customertypes = DB::table('customertype')->get();
        $governorates = DB::table('governorates')->get();
        $Salesperson = Salesperson::where('is_active', 1)->get();
        $cities = DB::table('cities')->get();
        // if ($request->has('search')) {
        if ($request['search']) {
            $key = explode(' ', $request['search']);
            $customers = User::with(['orders'])
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $customers = User::with(['orders']);
        }
        if ($request['fromDate']) {

            $query_param['fromDate'] =  $request['fromDate'];
            // echo $request['fromDate'];exit();
            $customers->where(DB::raw('date_format(users.created_at,"%Y-%m-%d")') , '>=' ,$request['fromDate'] );
        }
        if ($request['toDate']) {

            $query_param['toDate'] =  $request['toDate'];
            $customers->where(DB::raw('date_format(users.created_at,"%Y-%m-%d")') , '<=' ,$request['toDate'] );
        }
        if ($request['fromOrder']) {

            $query_param['fromOrder'] =  $request['fromOrder'];
            $customers->where(DB::raw('(SELECT COUNT(*) FROM `orders` WHERE orders.customer_id = users.id)') , '>=' ,$request['fromOrder'] );
        }
        if ($request['toOrder']) {

            $query_param['toOrder'] =  $request['toOrder'];
            $customers->where(DB::raw('(SELECT COUNT(*) FROM `orders` WHERE orders.customer_id = users.id)') , '<=' ,$request['toOrder'] );
        }
        if ($request['fromOrderprice']) {

            $query_param['fromOrderprice'] =  $request['fromOrderprice'];
            $customers->where(DB::raw('(SELECT sum(order_amount) FROM `orders` WHERE orders.customer_id = users.id)') , '>=' ,$request['fromOrderprice'] );
        }
        if ($request['toOrderprice']) {

            $query_param['toOrderprice'] =  $request['toOrderprice'];
            $customers->where(DB::raw('(SELECT sum(order_amount) FROM `orders` WHERE orders.customer_id = users.id)') , '<=' ,$request['toOrderprice'] );
        }
        if ($request['city']) {

            $query_param['city'] =  $request['city'];
            // $customers->where('users.city' , '=' ,$request['city'] );
            $customers->whereIn('users.city' ,$request['city'] );
        }
        if ($request['area']) {

            $query_param['area'] =  $request['area'];
            $customers->where('users.area' , '=' ,$request['area'] );
        }
        if ($request['type']) {

            $query_param['type'] =  $request['type'];

            $customers->where('users.type' , '=' ,$request['type'] );
        }
        if ($request['salesPersonId']) {

            $query_param['salesPersonId'] =  $request['salesPersonId'];

            $customers->where('users.salesPersonId' , '=' ,$request['salesPersonId'] );
        }
        // print_r($customers->toSql());
        // exit();



        $customers = $customers->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        foreach ($customers as &$customer) {
            $customer['_type']    = DB::table('customertype')->where('id',$customer['type'])->first();
            $customer['areaName'] = DB::table('cities')->where('id',$customer['area'])->first();
            $customer['cityName'] = DB::table('governorates')->where('id',$customer['city'])->first();
            $customer['salesPerson'] = Salesperson::where('id',$customer['salesPersonId'])->first();
            // if($customer['id']){
            //     echo "<pre>";
            //     print_r( $customer['_type']);
            //     echo "</pre>";
            // }
        }
        // exit();
        return view('admin-views.customer.list', compact('seaerchData' , 'customers', 'search' ,'customertypes','governorates','cities' , 'Salesperson'));


    }

    public function customer_export(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        $seaerchData['fromDate'] = isset($request['fromDate']) ? $request['fromDate'] : "";
        $seaerchData['toDate'] = isset($request['toDate']) ? $request['toDate'] : "";
        $seaerchData['fromOrder'] = isset($request['fromOrder']) ? $request['fromOrder'] : "";
        $seaerchData['fromOrderprice'] = isset($request['fromOrderprice']) ? $request['fromOrderprice'] : "";
        $seaerchData['toOrder'] = isset($request['toOrder']) ? $request['toOrder'] : "";
        $seaerchData['toOrderprice'] = isset($request['toOrderprice']) ? $request['toOrderprice'] : "";
        $seaerchData['city'] = isset($request['city']) ? explode(',',$request['city']) : [];
        $seaerchData['area'] = isset($request['area']) ? $request['area'] : "";
        $seaerchData['type'] = isset($request['type']) ? $request['type'] : "";
        $seaerchData['salesPersonId'] = isset($request['salesPersonId']) ? $request['salesPersonId'] : 0;
        $customertypes = DB::table('customertype')->get();
        $governorates = DB::table('governorates')->get();
        $cities = DB::table('cities')->get();
        // print_r($seaerchData['city']);exit();
        if ($request['search']) {
            $key = explode(' ', $request['search']);
            $customers = User::with(['orders'])
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $customers = User::with(['orders']);
        }
        if ($request['fromDate']) {

            $query_param['fromDate'] =  $request['fromDate'];
            // echo $request['fromDate'];exit();
            $customers->where(DB::raw('date_format(users.created_at,"%Y-%m-%d")') , '>=' ,$request['fromDate'] );
        }
        if ($request['toDate']) {

            $query_param['toDate'] =  $request['toDate'];
            $customers->where(DB::raw('date_format(users.created_at,"%Y-%m-%d")') , '<=' ,$request['toDate'] );
        }
        if ($request['fromOrder']) {

            $query_param['fromOrder'] =  $request['fromOrder'];
            $customers->where(DB::raw('(SELECT COUNT(*) FROM `orders` WHERE orders.customer_id = users.id)') , '>=' ,$request['fromOrder'] );
        }
        if ($request['toOrder']) {

            $query_param['toOrder'] =  $request['toOrder'];
            $customers->where(DB::raw('(SELECT COUNT(*) FROM `orders` WHERE orders.customer_id = users.id)') , '<=' ,$request['toOrder'] );
        }
        if ($request['fromOrderprice']) {

            $query_param['fromOrderprice'] =  $request['fromOrderprice'];
            $customers->where(DB::raw('(SELECT sum(order_amount) FROM `orders` WHERE orders.customer_id = users.id)') , '>=' ,$request['fromOrderprice'] );
        }
        if ($request['toOrderprice']) {

            $query_param['toOrderprice'] =  $request['toOrderprice'];
            $customers->where(DB::raw('(SELECT sum(order_amount) FROM `orders` WHERE orders.customer_id = users.id)') , '<=' ,$request['toOrderprice'] );
        }
        if ($request['city'] && $request['city'] != -1) {

            $query_param['city'] =  $request['city'];
            // $customers->where('users.city' , '=' ,$request['city'] );
            $customers->whereIn('users.city' ,$seaerchData['city'] );
        }
        if ($request['area']) {

            $query_param['area'] =  $request['area'];
            $customers->where('users.area' , '=' ,$request['area'] );
        }
        if ($request['type']) {

            $query_param['type'] =  $request['type'];

            $customers->where('users.type' , '=' ,$request['type'] );
        }
        if ($request['salesPersonId']) {

            $query_param['salesPersonId'] =  $request['salesPersonId'];

            $customers->where('users.salesPersonId' , '=' ,$request['salesPersonId'] );
        }
        // print_r($customers->toSql());
        // exit();

        $customers = $customers->get();
        $exportData = [];
        $exportData[] = [
            translate('Name'),
            translate('last_name'),
            translate('Email'),
            translate('Phone'),
            translate('RegisterDate'),
            translate('Total') . translate('Order'),
            translate('seller_amount'),
            translate('Salesperson'),
            translate('Type'),
            translate('city'),
            translate('area'),
            translate('GPS'),
            translate('getFrom')
        ];





        foreach ($customers as $customer) {

            $salesPerson = Salesperson::where('id',$customer['salesPersonId'])->first();
            $_type    = DB::table('customertype')->where('id',$customer['type'])->first();
            $areaName = DB::table('cities')->where('id',$customer['area'])->first();
            $cityName = DB::table('governorates')->where('id',$customer['city'])->first();
            $GPS = DB::table('shipping_addresses')->select(DB::raw('concat("https://www.google.com/maps?q=",`latitude`,",",`longitude`) as gps'))->orderByDesc('id')->where('customer_id',$customer['id']);
            // echo $GPS->toSql().'<br>'.$customer['id'].'<br>';
            $GPS = $GPS->first();
            $exportData[] = [
                $customer['f_name'],
                $customer['l_name'],
                $customer['email'],
                $customer['phone'],
                $customer['created_at'],
                $customer->orders->count(),
                $customer->orders->sum('order_amount'),
                isset($salesPerson) ? $salesPerson->f_name." ".$salesPerson->l_name : "",
                isset($_type) ? $_type->ar_name : "",
                isset($cityName) ? $cityName->governorate_name_ar : "",
                isset($areaName) ? $areaName->city_name_ar : "",
                isset($GPS->gps) ? $GPS->gps : "",
                translate($customer['getFrom']),
            ];
        }
            // exit;
        SimpleXLSXGen::fromArray( $exportData )->downloadAs('customers.xlsx');
        // print_r($exportData);
        // exit();
    }


    public function manage(Request $request , $id = 0)
    {
        $customertypes = DB::table('customertype')->get();
        $governorates = DB::table('governorates')->get();
        $cities = DB::table('cities')->get();

        $client = [];
        $addresses = [];
        if($id != 0 ){
            $client = User::where('id', $request['id'])->get()[0];
            $addresses = DB::table('shipping_addresses')->where('customer_id' , $request['id'])->get();
        }

        // print_r($client);exit();


        return view('admin-views.customer.add-new', compact('id','addresses','customertypes','governorates','cities','client'));
    }


    public function submit(Request $request)
    {
        // print_r($request['address']);
        // exit();
        // print_r($request['type']);
        // print_r($request['getFrom']);
        // exit();
        if($request['id'] == 0){


            $validation_array = [
                'f_name' => 'required',
                'phone' => 'unique:users',
                'password' => 'required|min:8|same:con_password'
            ];

            if($request['email'] != ''){
                $validation_array['email'] = 'email|unique:users';
            }
            $request->validate($validation_array, [
                'f_name.required' => 'First name is required',
            ]);

            $user = User::create([
                'f_name' => $request['f_name'],
                'l_name' => $request['l_name'],
                'email' => $request['email'],
                'phone' => $request['phone'],
                'city' => $request['city'],
                'area' => $request['area'],
                'type' => $request['type'],
                'getFrom' => $request['getFrom'],
                'is_active' => 1,
                'password' => bcrypt($request['password'])
            ]);

            $id = $user->id;
            $msg = 'Add_success_login_now';

        }else{

            $user = User::where('id', $request['id'])->get()[0];

            $validation =
            [
                'f_name' => 'required',
                'f_name.required' => 'First name is required',
            ];
            if($request['password']){
                $validation['password'] = 'required|min:8|same:con_password';
            }

            if($user['phone'] != $request['phone']){
                $validation['phone'] = 'unique:users';
            }

            if($user['email'] != $request['email'] && $request['email'] != ""){
                $validation['email'] = 'required|email|unique:users';
            }

            $request->validate($validation);
            $userData = [
                'f_name' => $request['f_name'],
                'l_name' => $request['l_name'],
                'email' => $request['email'],
                'phone' => $request['phone'],
                'city' => $request['city'],
                'area' => $request['area'],
                'type' => $request['type'],
                'getFrom' => $request['getFrom'],
                'is_active' => 1
            ];

            if($request['password']){
                $userData['password'] = bcrypt($request['password']);
            }

            User::where('id', $request['id'])->update($userData);
            $id = $request['id'];

            $msg = 'update_success_login_now';
        }

        $request['address'] = isset($request['address']) ? $request['address'] : [];
        foreach ($request['address'] as $address) {
            if(isset($address['id'])){
                $_address = [
                    'customer_id' => $id,
                    'contact_person_name' => $address['name'],
                    'address_type' => $address['addressAs'],
                    'address' => $address['address'],
                    'city' => $address['city'],
                    'zip' => $address['zip'],
                    'phone' => $address['phone'],
                    'is_billing' =>$address['is_billing'],
                    'latitude' =>isset($address['latitude']) ? $address['latitude'] : 0,
                    'longitude' =>isset($address['longitude']) ? $address['longitude'] : 0,
                ];
                DB::table('shipping_addresses')->where('id',$address['id'])->update($_address);
            }
            else{
                $_address = [
                    'customer_id' => $id,
                    'contact_person_name' => $address['name'],
                    'address_type' => $address['addressAs'],
                    'address' => $address['address'],
                    'city' => $address['city'],
                    'zip' => $address['zip'],
                    'phone' => $address['phone'],
                    'is_billing' =>$address['is_billing'],
                    'latitude' =>isset($address['latitude']) ? $address['latitude'] : 0,
                    'longitude' =>isset($address['longitude']) ? $address['longitude'] : 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                DB::table('shipping_addresses')->insert($_address);
            }
        }

        Toastr::success(translate($msg));
        return redirect(route('admin.customer.list'));
    }

    public function status_update(Request $request)
    {
        User::where(['id' => $request['id']])->update([
            'is_active' => $request['status']
        ]);

        DB::table('oauth_access_tokens')
            ->where('user_id', $request['id'])
            ->delete();

        return response()->json([], 200);
    }

    public function view(Request $request, $id)
    {

        $customer = User::find($id);
        if (isset($customer)) {

            $delivery_men = Salesperson::where('is_active', 1)->get();

            $query_param = [];
            $search = $request['search'];
            $orders = Order::where(['customer_id' => $id]);
            if ($request->has('search')) {

                $orders = $orders->where('id', 'like', "%{$search}%");
                $query_param = ['search' => $request['search']];
            }
            $orders = $orders->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
            return view('admin-views.customer.customer-view', compact('delivery_men' , 'customer', 'orders', 'search'));
        }
        Toastr::error('Customer not found!');
        return back();
    }
    public function delete($id)
    {
        $customer = User::find($id);
        $customer->delete();
        Toastr::success('Customer deleted successfully!');
        return back();
    }

    public function subscriber_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $subscription_list = Subscription::where('email','like', "%{$search}%");

            $query_param = ['search' => $request['search']];
        } else {
        $subscription_list = new Subscription;
        }
        $subscription_list = $subscription_list->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.customer.subscriber-list',compact('subscription_list','search'));
    }
    public function customer_settings()
    {
        $data = BusinessSetting::where('type','like','wallet_%')->orWhere('type','like','loyalty_point_%')->get();
        $data = array_column($data->toArray(), 'value','type');

        return view('admin-views.customer.customer-settings', compact('data'));
    }

    public function customer_update_settings(Request $request)
    {

        $request->validate([
            'add_fund_bonus'=>'nullable|numeric|max:100|min:0',
            'loyalty_point_exchange_rate'=>'nullable|numeric',
        ]);
        BusinessSetting::updateOrInsert(['type' => 'wallet_status'], [
            'value' => $request['customer_wallet']??0
        ]);
        BusinessSetting::updateOrInsert(['type' => 'loyalty_point_status'], [
            'value' => $request['customer_loyalty_point']??0
        ]);
        BusinessSetting::updateOrInsert(['type' => 'wallet_add_refund'], [
            'value' => $request['refund_to_wallet']??0
        ]);
        BusinessSetting::updateOrInsert(['type' => 'loyalty_point_exchange_rate'], [
            'value' => $request['loyalty_point_exchange_rate']??0
        ]);
        BusinessSetting::updateOrInsert(['type' => 'loyalty_point_item_purchase_point'], [
            'value' => $request['item_purchase_point']??0
        ]);
        BusinessSetting::updateOrInsert(['type' => 'loyalty_point_minimum_point'], [
            'value' => $request['minimun_transfer_point']??0
        ]);
        BusinessSetting::updateOrInsert(['type' => 'walletOrderWithRate'], [
            'value' => $request['walletOrderWithRate']??0
        ]);
        BusinessSetting::updateOrInsert(['type' => 'walletOrderValue'], [
            'value' => $request['walletOrderValue']??0
        ]);

        Toastr::success(translate('customer_settings_updated_successfully'));
        return back();
    }

    public function get_customers(Request $request){
        $key = explode(' ', $request['q']);
        $data = User::where('id','!=',0)->
        where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                ->orWhere('l_name', 'like', "%{$value}%")
                ->orWhere('phone', 'like', "%{$value}%");
            }
        })
        ->limit(8)
        ->get([DB::raw('id, CONCAT(f_name, " ", l_name, " (", phone ,")") as text')]);
        if($request->all) $data[]=(object)['id'=>false, 'text'=>trans('messages.all')];


        return response()->json($data);
    }




    public function add_Salesperson(Request $request , $customerId, $salesPersonId)
    {

        if ($salesPersonId == 0) {
            return response()->json([], 401);
        }
        $customer = User::find($customerId);
        $customer->salesPersonId = $salesPersonId;
        $customer->save();



        return response()->json(['status' => true], 200);
    }

}
