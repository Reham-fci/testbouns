<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\CategoryManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Category;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function get_categories()
    {
        try {
            $categories = Category::with(['childes.childes'])->where(['position' => 0])->priority()->get();
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_products($id)
    {   
        //  $products=Helpers::product_data_formatting(CategoryManager::products($id), true);
        //  foreach ($products as $key => $product) {
        //     if(!isset($products[$key]['variation'][0])){
        //       $products[$key]['variation'] = [
        //             [
        //                 "type" => $product->unit,
        //                 "price"=> $product->unit_price,
        //                 "sku"  => null,
        //                 "qty"  => $product->current_stock   
        //             ]
        //       ];
        //     }
        //  }
         $sub=CategoryManager::child($id);
         $products = [];
        return response()->json(['products'=>$products,'sub_categories'=>$sub], 200);
    }
        public function get_products_by_sub($category_id,$sub)
    {   
         $products=Helpers::product_data_formatting(CategoryManager::products($sub), true);
        //  $brands = DB::table('brands')->where('id in (SELECT brand_id FROM `products` where products.category_ids like \'%"id":"'.$sub.'"%\')')->get();
         $brands = DB::table('brands')
            ->whereIn('id', function($query ) use($sub){
                $query->select('brand_id')
                ->from('products')
                ->where('category_ids','like', "%\"id\":\"".$sub."\"%");
            })
        ->get();
        
         $sub=CategoryManager::child($category_id);
        return response()->json(['products'=>$products,'sub_categories'=>$sub , "brands" => $brands], 200);
    }
    
    
    
}
