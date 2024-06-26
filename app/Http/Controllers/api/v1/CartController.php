<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use function App\CPU\translate;

class CartController extends Controller
{

    public function cart(Request $request)
    {
        $user = Helpers::get_customer($request);
        $item = [];
        $cart = Cart::
            select(DB::raw("carts.* , current_stock"))
            ->join('products', 'products.id', '=', 'product_id')
            ->where(['customer_id' => $user->id])->get();
            
        // $cart->map(function ($data) {
        foreach($cart as &$data){
            
            $data['choices'] = json_decode($data['choices']);
            $data['variations'] = json_decode($data['variations']);
            if(isset($item[$data['product_id']])){
                
                $item[$data['product_id']] = $item[$data['product_id']] - ($data['numberOfPieces'] * $data['quantity']);
            }
            else{
                
                $item[$data['product_id']] = $data['current_stock'] - ($data['numberOfPieces'] * $data['quantity']);
            }
            // $data['test'] = &$item[$data['product_id']];
            $data['available_items'] = intval($data['current_stock'] / $data['numberOfPieces']);
            // return $data;
        }
        // });
        
        // dd($item);
        foreach($cart as &$data){
            $data['OutOfStock'] = $item[$data['product_id']] >= 0 ? 0 : 1;
            // return $data;
        }
        
        return response()->json($cart, 200);
    }

    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ], [
            'id.required' => translate('Product ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $cart = CartManager::add_to_cart($request);
        return response()->json($cart, 200);
    }

    public function update_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'quantity' => 'required',
        ], [
            'key.required' => translate('Cart key or ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $response = CartManager::update_cart_qty($request);
        return response()->json($response);
    }

    public function remove_from_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required'
        ], [
            'key.required' => translate('Cart key or ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $user = Helpers::get_customer($request);
        Cart::where(['id' => $request->key, 'customer_id' => $user->id])->delete();
        return response()->json(translate('successfully_removed'));
    }
    public function remove_all_from_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required'
        ], [
            'key.required' => translate('Cart key or ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $user = Helpers::get_customer($request);
        Cart::where(['customer_id' => $user->id])->delete();
        return response()->json(translate('successfully_removed'));
    }
}
