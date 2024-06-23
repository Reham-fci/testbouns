<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\Helpers;
use App\CPU\ProductManager;
use App\Http\Controllers\Controller;
use App\Model\Color;
use App\Model\Currency;
use App\Model\HelpTopic;
use Illuminate\Http\Request;
use App\Model\ShippingType;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function getcities()
    {
        $data = [];
        $data['cities'] = DB::table('governorates')->get();
        foreach($data['cities'] as $cities){
            $cities->id = "".$cities->id."";
        }
        $data['customertype'] = DB::table('customertype')->get();
        foreach($data['customertype'] as $customertype){
            $customertype->id = "".$customertype->id."";
        }
        $data['getFrom'] = [
            'type_facebook' => 'فيس بوك ',
            'type_representative' => 'مندوب ',
            'type_nomination' => 'ترشيح من تاجر ',
            'type_call' => 'مكالمة ',
            'type_linkdin' => 'لينكدان ',
            'type_Google' => 'جوجل ',
            'type_other' => 'اخري',
        ];
        foreach ($data['cities'] as $governorate) {
            $governorate->areas = DB::table('cities')->where('governorate_id' , $governorate->id)->get();
            foreach($governorate->areas as $area){
                $area->id = "".$area->id."";
                $area->governorate_id = "".$area->governorate_id."";
            }
        }
        return response()->json($data);
    }


    public function checkout_Limit($area){
        // SELECT `id`, `OrderLimitID`, `areaId` FROM `orderLimitArea` WHERE 1
        // SELECT `id`, `created_at`, `Limit` FROM `orderLimit` WHERE 1
        // $area = $request->area; 
        $result = DB::table('orderLimitArea')
        ->select('orderLimit.Limit')
        ->join('orderLimit', 'orderLimit.id', '=', 'orderLimitArea.OrderLimitID')
        ->where('areaId', '=' , $area)
        ->orderBy('orderLimit.id', 'desc')
        ->get();

        if(!isset($result[0])){
            $result = [];
            $result[0] = [
                    'Limit' => "0"
                ];
        }
        else{
            $result[0]->Limit = "".$result[0]->Limit."";
        }
        
        $response = [
            'limit' => $result,
        ];

        return response()->json($response);
    }

    public function configuration()
    {
        $currency = Currency::all();
        $social_login = [];
        foreach (Helpers::get_business_settings('social_login') as $social) {
            $config = [
                'login_medium' => $social['login_medium'],
                'status' => (boolean)$social['status']
            ];
            array_push($social_login, $config);
        }

        $languages = Helpers::get_business_settings('pnc_language');
        $lang_array = [];
        foreach ($languages as $language) {
            array_push($lang_array, [
                'code' => $language,
                'name' => Helpers::get_language_name($language)
            ]);
        }

        $admin_shipping = ShippingType::where('seller_id',0)->first();
        $shipping_type = isset($admin_shipping)==true?$admin_shipping->shipping_type:'order_wise';

        return response()->json([
            'system_default_currency' => (int)Helpers::get_business_settings('system_default_currency'),
            'digital_payment' => (boolean)Helpers::get_business_settings('digital_payment')['status'] ?? 0,
            'cash_on_delivery' => (boolean)Helpers::get_business_settings('cash_on_delivery')['status'] ?? 0,
            'base_urls' => [
                'product_image_url' => ProductManager::product_image_path('product'),
                'product_thumbnail_url' => ProductManager::product_image_path('thumbnail'),
                'brand_image_url' => asset('storage/app/public/brand'),
                'customer_image_url' => asset('storage/app/public/profile'),
                'banner_image_url' => asset('storage/app/public/banner'),
                'category_image_url' => asset('storage/app/public/category'),
                'review_image_url' => asset('storage/app/public'),
                'seller_image_url' => asset('storage/app/public/seller'),
                'shop_image_url' => asset('storage/app/public/shop'),
                'notification_image_url' => asset('storage/app/public/notification'),
            ],
            'static_urls' => [
                'contact_us' => route('contacts'),
                'brands' => route('brands'),
                'categories' => route('categories'),
                'customer_account' => route('user-account'),
            ],
            'about_us' => Helpers::get_business_settings('about_us'),
            'privacy_policy' => Helpers::get_business_settings('privacy_policy'),
            'faq' => HelpTopic::all(),
            'terms_&_conditions' => Helpers::get_business_settings('terms_condition'),
            'currency_list' => $currency,
            'currency_symbol_position' => Helpers::get_business_settings('currency_symbol_position') ?? 'right',
            'business_mode'=> Helpers::get_business_settings('business_mode'),
            'maintenance_mode' => (boolean)Helpers::get_business_settings('maintenance_mode') ?? 0,
            'language' => $lang_array,
            'colors' => Color::all(),
            'unit' => Helpers::units(),
            'shipping_method' => Helpers::get_business_settings('shipping_method'),
            'email_verification' => (boolean)Helpers::get_business_settings('email_verification'),
            'phone_verification' => (boolean)Helpers::get_business_settings('phone_verification'),
            'country_code' => Helpers::get_business_settings('country_code'),
            'social_login' => $social_login,
            'currency_model' => Helpers::get_business_settings('currency_model'),
            'forgot_password_verification' => Helpers::get_business_settings('forgot_password_verification'),
            'announcement'=> Helpers::get_business_settings('announcement'),
            'pixel_analytics'=> Helpers::get_business_settings('pixel_analytics'),
            'software_version'=>env('SOFTWARE_VERSION'),
            'decimal_point_settings'=>Helpers::get_business_settings('decimal_point_settings'),
            'inhouse_selected_shipping_type'=>$shipping_type,
            'billing_input_by_customer'=>Helpers::get_business_settings('billing_input_by_customer'),
            'wallet_status'=>Helpers::get_business_settings('wallet_status'),
            'loyalty_point_status'=>Helpers::get_business_settings('loyalty_point_status'),
            'loyalty_point_exchange_rate'=>Helpers::get_business_settings('loyalty_point_exchange_rate'),
            'loyalty_point_minimum_point'=>Helpers::get_business_settings('loyalty_point_minimum_point'),
            'bonus_facebook'=>Helpers::get_business_settings('bonus_facebook'),
            'bonus_WhatsApp'=>Helpers::get_business_settings('bonus_WhatsApp'),
            'bonus_phone'=>Helpers::get_business_settings('bonus_phone'),
            'bonus_google_play'=>Helpers::get_business_settings('bonus_google_play'),

        ]);
    }
}

