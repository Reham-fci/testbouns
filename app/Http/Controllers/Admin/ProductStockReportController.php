<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
require __DIR__ .'/SimpleXLSXGen.php';
use SimpleXLSXGen;

class ProductStockReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('seller_id') == false || $request['seller_id'] == 'all') {
            $query = Product::whereIn('added_by', ['admin', 'seller']);
        } elseif ($request['seller_id'] == 'in_house') {
            $query = Product::where(['added_by' => 'admin']);
        } else {
            $query = Product::where(['added_by' => 'seller', 'user_id' => $request['seller_id']]);
        }

        $query_param = ['seller_id' => $request['seller_id']];
        $products = $query->paginate(Helpers::pagination_limit())->appends($query_param);
        $seller_is = $request['seller_id'];
        return view('admin-views.report.product-stock', compact('products','seller_is'));
    }
    
    
    
    public function bulk_export_data()
    {
        $products = Product::
            select(DB::raw("products.name , products.current_stock
            "))
            // ->where(['added_by' => 'admin'])
            ->get();
        //export from product
        $storage = [];
        $storage[] = [
            'name' => 'name',
            'current_stock' => 'current_stock',
        ];
        foreach ($products as $item) {
            
            $storage[] = [
                'name' => $item->name,
                'current_stock' => $item->current_stock,
            ];
        }
        SimpleXLSXGen::fromArray( $storage )->downloadAs('stock.xlsx');
    }

}
