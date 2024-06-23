<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductScript extends Controller
{
    
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function run()
    {
        $products = DB::table('products')->get();
        foreach($products as $product){
            if($product->variation != "[]")
            {
                $variations = json_decode($product->variation);
                $options = [];
                foreach($variations as $variation){
                    // print_r($variation);exit;;
                    if(!isset($variation->purchase_price)){
                        
                        $arabic = json_decode('"' . $variation->type . '"');
                        preg_match_all('!\d+!', $arabic, $matches);
                        // print_r($matches[0]);exit;
                        $_option = [
                            'type' => $variation->type,
                            'price' => $variation->price,
                            'qty' => 0,
                            'sku' => null,
                            'purchase_price' => $variation->price,
                            'tax' => $product->tax,
                            'discount' => $product->discount,
                            'discount_type' => $product->discount_type,
                            'tax_type' => $product->tax_type,
                            'shipping_cost' => $product->shipping_cost,
                            'multiply_qty' => isset($product->multiplyQTY) ? $product->multiplyQTY : 0,
                            'numberOfPieces' =>  isset($matches[0][0]) ? $matches[0][0] : 1,
                        ];
                        $options[] = $_option;
                        // $_option['productId'] = $product->id;
                        // DB::table('product_variations')->insert($_option);
                    }
                }
                DB::table('products')->where('id' , $product->id)->update([
                        'variation' => json_encode($options)
                    ]);
                        
            }
            
        }
        
    }
}