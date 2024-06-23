<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\BrandManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function get_brands()
    {
        try {
            $brands = BrandManager::get_brands();
        } catch (\Exception $e) {
        }

        return response()->json($brands,200);
    }

    public function get_products($brand_id)
    {          
        try {
            
            $products = BrandManager::get_products($brand_id);
            $sub      =BrandManager::get_sub_category($brand_id);
          
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }

        return response()->json(['products'=>$products,'sub_categories'=>$sub],200);
    }
    //new function get product by sub
    public function get_products_by_sub($brand_id,$sub_id){
        
      try {
            
            $products = BrandManager::get_products_by_sub($brand_id,$sub_id);
            $sub      =BrandManager::get_sub_category($brand_id);
          
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }

        return response()->json(['products'=>$products,'sub_categories'=>$sub],200);
    
        
    }
}
