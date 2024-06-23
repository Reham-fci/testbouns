<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\FlashDeal;
use Illuminate\Http\Request;

class DealController extends Controller
{
    
    public function get_featured_deal()
    {
        $featured_deal = FlashDeal::with(['products.product.reviews','products.product.rating'])
            ->where(['status' => 1])
            ->where(['deal_type' => 'feature_deal'])->first();
        $data = [];
        if(isset($featured_deal)){
            $featured_deal = $featured_deal->products->map( function ($data) {
                try{
                    $data->product = Helpers::product_data_formatting($data->product,false);
                }catch(\Exception $ex){}
                return $data;
            });
        }
        foreach($featured_deal as $_featured_deal){
            if($_featured_deal->product){
                $data[] = $_featured_deal;
            }
        }
        // return response()->json($featured_deal, 200);
        return response()->json($data, 200);
    }
}
