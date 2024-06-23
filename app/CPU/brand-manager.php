<?php

namespace App\CPU;

use App\Model\Brand;
use App\Model\Product;
use App\Model\Category;

class BrandManager
{
    public static function get_brands()
    {
        return Brand::withCount('brandProducts')->latest()->get();
    }
    //new
    public static function get_brands_sub($brand_id)
    {
            $cat=Product::active()->where(['brand_id' => $brand_id,])
             ->select('category_ids')
            ->get()
           ;
           $ids=[];
           
            foreach($cat as $c){
                $item=json_decode($c['category_ids']);
                
                if(isset($item[1])&& $item[1]->position==2){
                $ids[]= $item[1]->id;
                }
            }
            
            $categories = Category::where(['position' => 1])
            ->whereIn('id',$ids)
            ->priority()->get();
            return $categories;
        
        return Brand::withCount('brandProducts')->latest()->get();
    }

    public static function get_products($brand_id)
    {
        return Helpers::product_data_formatting(Product::active()->where(['brand_id' => $brand_id])->get(), true);
    }
    
    
    //get products by brans and sub 
        public static function get_products_by_sub($brand_id,$sub_id)
    {
        return Helpers::product_data_formatting(Product::active()
        ->where(['brand_id' => $brand_id])
        ->where('category_ids', 'like', "%{$sub_id}%")
        ->get(), true);
    }
    
        //new api to get brand sub categories
        public static function get_sub_category($brand_id)
    {
        $cat=Product::active()->where(['brand_id' => $brand_id,])
         ->select('category_ids')
        ->get()
       ;
       $ids=[];
       
        foreach($cat as $c){
            $item=json_decode($c['category_ids']);
            
            if(isset($item[1])&& $item[1]->position==2){
            $ids[]= $item[1]->id;
            }
        }
        
        $categories = Category::where(['position' => 1])
        ->whereIn('id',$ids)
        ->priority()->get();
        return $categories;
    }
    
    
}
