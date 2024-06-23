<?php

namespace App\Http\Controllers\Admin;

require __DIR__ .'/SimpleXLSXGen.php';
use App\CPU\BackEndHelper;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\BaseController;
use App\Model\Brand;
use App\Model\Category;
use App\Model\Color;
use App\Model\DealOfTheDay;
use App\Model\FlashDealProduct;
use App\Model\Product;
use App\Model\Review;
use App\Model\Translation;
use App\Model\Wishlist;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;
use App\Model\Cart;

use SimpleXLSXGen;
class ProductController extends BaseController
{
    public function add_new()
    {
        $cat = Category::where(['parent_id' => 0])->get();
        $br = Brand::orderBY('name', 'ASC')->get();
        // return view('admin-views.product.add-new', compact('cat', 'br'));
        return view('admin-views.product2.add-new', compact('cat', 'br'));
    }
    public function add_type(Request $request)
    {
        $id = DB::table('units')->insertGetId(
            [
                'name_ar' => $request->name,
                'name_en' => $request->name,
            ]
        );
       echo json_encode(array('status' => $id));
    }

    public function featured_status(Request $request)
    {
        $product = Product::find($request->id);
        $product->featured = ($product['featured'] == 0 || $product['featured'] == null) ? 1 : 0;
        $product->save();
        $data = $request->status;
        return response()->json($data);
    }

    public function approve_status(Request $request)
    {
        $product = Product::find($request->id);
        $product->request_status = ($product['request_status'] == 0) ? 1 : 0;
        $product->save();

        return redirect()->route('admin.product.list', ['seller', 'status' => $product['request_status']]);
    }

    public function deny(Request $request)
    {
        $product = Product::find($request->id);
        $product->request_status = 2;
        $product->denied_note = $request->denied_note;
        $product->save();

        return redirect()->route('admin.product.list', ['seller', 'status' => 2]);
    }

    public function view($id)
    {
        $product = Product::with(['reviews'])->where(['id' => $id])->first();
        $reviews = Review::where(['product_id' => $id])->paginate(Helpers::pagination_limit());
        return view('admin-views.product.view', compact('product', 'reviews'));
    }

    public function store_old(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'brand_id' => 'required',
            'unit' => 'required',
            'images' => 'required',
            'image' => 'required',
            'tax' => 'required|min:0',
            'unit_price' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric|min:1',
            'discount' => 'required|gt:-1',
            'shipping_cost' => 'required|gt:-1',
        ], [
            'images.required' => 'Product images is required!',
            'image.required' => 'Product thumbnail is required!',
            'category_id.required' => 'category  is required!',
            'brand_id.required' => 'brand  is required!',
            'unit.required' => 'Unit  is required!',
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['unit_price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['unit_price'] <= $dis) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'unit_price', 'Discount can not be more or equal to the price!'
                );
            });
        }


        $p = new Product();
        $p->user_id = auth('admin')->id();
        $p->added_by = "admin";
        $p->name = $request->name[array_search('en', $request->lang)];
        $p->slug = Str::slug($request->name[array_search('en', $request->lang)], '-') . '-' . Str::random(6);

        $category = [];

        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }

        $p->category_ids = json_encode($category);
        $p->brand_id = $request->brand_id;
        $p->unit = $request->unit;
        $p->details = $request->description[array_search('en', $request->lang)];

        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $p->colors = json_encode($request->colors);
        } else {
            $colors = [];
            $p->colors = json_encode($colors);
        }
        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', $request[$str]));
                array_push($choice_options, $item);
            }
        }
        $p->choice_options = json_encode($choice_options);
        //combinations start
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options

        $combinations = Helpers::combinations($options);

        $variations = [];
        $stock_count = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                            $color_name = Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = BackEndHelper::currency_to_usd(abs($request['price_' . str_replace('.', '_', $str)]));
                $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
                $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
                $stock_count += $item['qty'];
            }
        } else {
            $stock_count = (integer)$request['current_stock'];
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        //combinations end
        $p->variation = json_encode($variations);
        $p->unit_price = BackEndHelper::currency_to_usd($request->unit_price);
        $p->purchase_price = BackEndHelper::currency_to_usd($request->purchase_price);
        $p->tax = $request->tax_type == 'flat' ? BackEndHelper::currency_to_usd($request->tax) : $request->tax;
        $p->tax_type = $request->tax_type;
        $p->discount = $request->discount_type == 'flat' ? BackEndHelper::currency_to_usd($request->discount) : $request->discount;
        $p->discount_type = $request->discount_type;
        $p->attributes = json_encode($request->choice_attributes);
        $p->current_stock = abs($stock_count);

        $p->video_provider = 'youtube';
        $p->video_url = $request->video_link;
        $p->request_status = 1;
        $p->shipping_cost = BackEndHelper::currency_to_usd($request->shipping_cost);
        $p->multiply_qty = $request->multiplyQTY=='on'?1:0;

        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('product/', 'png', $img);
                }
                $p->images = json_encode($product_images);
            }
            $p->thumbnail = ImageManager::upload('product/thumbnail/', 'png', $request->image);

            $p->meta_title = $request->meta_title;
            $p->meta_description = $request->meta_description;
            $p->meta_image = ImageManager::upload('product/meta/', 'png', $request->meta_image);

            $p->save();

            $data = [];
            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    array_push($data, array(
                        'translationable_type' => 'App\Model\Product',
                        'translationable_id' => $p->id,
                        'locale' => $key,
                        'key' => 'name',
                        'value' => $request->name[$index],
                    ));
                }
                if ($request->description[$index] && $key != 'en') {
                    array_push($data, array(
                        'translationable_type' => 'App\Model\Product',
                        'translationable_id' => $p->id,
                        'locale' => $key,
                        'key' => 'description',
                        'value' => $request->description[$index],
                    ));
                }
            }
            Translation::insert($data);

            Toastr::success(translate('Product added successfully!'));
            return redirect()->route('admin.product.list', ['in_house']);
        }
    }
    
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'brand_id' => 'required',
            'images' => 'required',
            'image' => 'required',
        ], [
            'images.required' => 'Product images is required!',
            'image.required' => 'Product thumbnail is required!',
            'category_id.required' => 'category  is required!',
            'brand_id.required' => 'brand  is required!',
        ]);

        
        $variations = [];
        $_variations = [];
        $productOptionsNames = [] ;
        foreach($request['prdoctPrice'] as $option){
            if($option['unit'] && $option['unit_price'] && $option['purchase_price']  && $option['numberOfPieces'] ){
    
                if ($option['discount_type'] == 'percent') {
                    $option['dis'] = ($option['unit_price'] / 100) * $option['discount'];
                } else {
                    $option['dis'] = $option['discount'];
                }
        
                if ($option['unit_price'] <= $option['dis']) {
                    $validator->after(function ($validator) {
                        $validator->errors()->add(
                            'unit_price', 'Discount can not be more or equal to the price!'
                        );
                    });
                }
                
                $_option = [
                    'order' => $option['order'],
                    'type' => $option['unit'],
                    'price' => $option['unit_price'],
                    'qty' => 0,
                    'sku' => null,
                    'purchase_price' => $option['purchase_price'],
                    'tax' => $option['tax'],
                    'discount' => $option['discount'],
                    'discount_type' => $option['discount_type'],
                    'tax_type' => $option['tax_type'],
                    'shipping_cost' => $option['shipping_cost'],
                    'multiply_qty' => isset($option['multiplyQTY']) ? $option['multiplyQTY'] : 0,
                    'numberOfPieces' =>  $option['numberOfPieces'],
                    'description' =>  $option['description'],
                ];
                $_variations[$option['order']] = $_option;
                // $productOptionsNames[] = $_option['type'];
            }
        }

        if(count($_variations) == 0){
            $validator->after(function ($validator) {
                $validator->errors()->add('options', 'Product can not have one unit');
            });
        }
        
        
        sort($_variations);
        foreach($_variations as $_variation){
                $variations[] = $_variation;
                $productOptionsNames[] = $_variation['type'];
        }
        
        unset($_variations);
        
        $choice_options = [];
        array_push($choice_options, [
            "name" => "choice_4",
            "title" => "الكمية",
            "options" => $productOptionsNames
        ]);
        



        $p = new Product();
        $p->user_id = auth('admin')->id();
        $p->added_by = "admin";
        $p->name = $request->name[array_search('en', $request->lang)];
        $p->slug = Str::slug($request->name[array_search('en', $request->lang)], '-') . '-' . Str::random(6);

        $category = [];

        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }

        $p->category_ids = json_encode($category);
        $p->brand_id = $request->brand_id;
        $p->unit = $request->unit;
        $p->details = $request->description[array_search('en', $request->lang)];

        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $p->colors = json_encode($request->colors);
        } else {
            $colors = [];
            $p->colors = json_encode($colors);
        }
        
        $p->choice_options = json_encode($choice_options);
        

        $stock_count = (integer)$request['current_stock'];

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        //combinations end
        $p->variation = json_encode($variations);
        $p->unit_price = BackEndHelper::currency_to_usd($request->unit_price);
        $p->purchase_price = BackEndHelper::currency_to_usd($request->purchase_price);
        $p->tax = $request->tax_type == 'flat' ? BackEndHelper::currency_to_usd($request->tax) : $request->tax;
        $p->tax_type = $request->tax_type;
        $p->discount = $request->discount_type == 'flat' ? BackEndHelper::currency_to_usd($request->discount) : $request->discount;
        $p->discount_type = $request->discount_type;
        $p->attributes = json_encode($request->choice_attributes);
        $p->current_stock = abs($stock_count);

        $p->video_provider = 'youtube';
        $p->video_url = $request->video_link;
        $p->request_status = 1;
        $p->shipping_cost = BackEndHelper::currency_to_usd($request->shipping_cost);
        $p->multiply_qty = $request->multiplyQTY=='on'?1:0;

        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('product/', 'png', $img);
                }
                $p->images = json_encode($product_images);
            }
            $p->thumbnail = ImageManager::upload('product/thumbnail/', 'png', $request->image);

            $p->meta_title = $request->meta_title;
            $p->meta_description = $request->meta_description;
            $p->meta_image = ImageManager::upload('product/meta/', 'png', $request->meta_image);

            $p->save();

            $data = [];
            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    array_push($data, array(
                        'translationable_type' => 'App\Model\Product',
                        'translationable_id' => $p->id,
                        'locale' => $key,
                        'key' => 'name',
                        'value' => $request->name[$index],
                    ));
                }
                if ($request->description[$index] && $key != 'en') {
                    array_push($data, array(
                        'translationable_type' => 'App\Model\Product',
                        'translationable_id' => $p->id,
                        'locale' => $key,
                        'key' => 'description',
                        'value' => $request->description[$index],
                    ));
                }
            }
            Translation::insert($data);
            foreach($variations as $variation){
                $variation['productId'] = $p->id;
                DB::table('product_variations')->insert($variation);
            }
            Toastr::success(translate('Product added successfully!'));
            return redirect()->route('admin.product.list', ['in_house']);
        }
    }
    



    function list(Request $request, $type)
    {
        $query_param = [];
        $search = $request['search'];
        $pro = Product::select(DB::raw("* , 
            (select concat(min(price),' : ',max(price)) from product_variations where product_variations.productId = products.id) as price2,
            (select concat(min(purchase_price),' : ',max(purchase_price)) from product_variations where product_variations.productId = products.id) as purchase_price2
        "));
        if ($type == 'in_house') {
            $pro = $pro->where(['added_by' => 'admin']);
        } else {
            $pro = $pro->where(['added_by' => 'seller'])->where('request_status', $request->status);
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $pro->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $request_status = $request['status'];
        if(isset($request->testDeveloper)){
            echo $pro->toSql();exit;
        }
        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends(['status' => $request['status']])->appends($query_param);
        return view('admin-views.product2.list', compact('pro', 'search', 'request_status'));
    }

    public function updated_product_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = Product::where(['added_by' => 'seller'])
                ->where('is_shipping_cost_updated',0)
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->Where('name', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $pro = Product::where(['added_by' => 'seller'])->where('is_shipping_cost_updated',0);
        }
        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.product.updated-product-list', compact('pro', 'search'));
    }

    public function stock_limit_list(Request $request, $type)
    {
        $stock_limit = Helpers::get_business_settings('stock_limit');
        $sort_oqrderQty = $request['sort_oqrderQty'];
        $query_param = $request->all();
        $search = $request['search'];
        if ($type == 'in_house') {
            $pro = Product::where(['added_by' => 'admin']);
        } else {
            $pro = Product::where(['added_by' => 'seller'])->where('request_status', $request->status);
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $pro->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $request_status = $request['status'];

        $pro = $pro->withCount('order_details')->when($request->sort_oqrderQty == 'quantity_asc', function ($q) use ($request) {
            return $q->orderBy('current_stock', 'asc');
        })
            ->when($request->sort_oqrderQty == 'quantity_desc', function ($q) use ($request) {
                return $q->orderBy('current_stock', 'desc');
            })
            ->when($request->sort_oqrderQty == 'order_asc', function ($q) use ($request) {
                return $q->orderBy('order_details_count', 'asc');
            })
            ->when($request->sort_oqrderQty == 'order_desc', function ($q) use ($request) {
                return $q->orderBy('order_details_count', 'desc');
            })
            ->when($request->sort_oqrderQty == 'default', function ($q) use ($request) {
                return $q->orderBy('id');
            })
            // ->where('current_stock', '<', $stock_limit)
            ;

        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends(['status' => $request['status']])->appends($query_param);
        return view('admin-views.product.stock-limit-list', compact('pro', 'search', 'request_status', 'sort_oqrderQty'));
    }

    public function update_quantity(Request $request)
    {
        
        $productVariations = DB::table('product_variations')->where('productId',$request['product_id'])->orderBy('order', 'ASC')->orderBy('id', 'ASC')->get();
        $product = Product::find($request['product_id']);
        
        $variations = [];
        $index = 0;
        foreach($productVariations as $option){
            
            DB::table('product_variations')->where('id' , $option->id)->update([
                    'price' => $request['price_' .  str_replace(' ', '_', $option->type)],
                ]);
            $variations[] = [
                'order' => $option->order,
                'type' => $option->type,
                'price' => $request['price_' .  str_replace(' ', '_', $option->type)],
                'qty' => 0,
                'sku' => null,
                'purchase_price' => $option->purchase_price,
                'tax' => $option->tax,
                'discount' => $option->discount,
                'discount_type' => $option->discount_type,
                'tax_type' => $option->tax_type,
                'shipping_cost' => $option->shipping_cost,
                'multiply_qty' => isset($option->multiplyQTY) ? $option->multiplyQTY : 0,
                'numberOfPieces' =>  $option->numberOfPieces,
                'description' =>  $option->description,
            ];
            echo ( 'price_' . $option->type." : " . $request['price_' . $option->type])."<br>";
        }
        
        $stock_count = $request['current_stock'];
        // if ($request->has('type')) {
        //     foreach ($request['type'] as $key => $str) {
        //         $item = [];
        //         $item['type'] = $str;
        //         $item['price'] = BackEndHelper::currency_to_usd(abs($request['price_' . str_replace('.', '_', $str)]));
        //         $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
        //         $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
        //         array_push($variations, $item);
        //     }
        // }

        if ($stock_count >= 0) {
            $product->current_stock = $stock_count;
            $product->variation = json_encode($variations);
            $product->save();
            Toastr::success(\App\CPU\translate('product_quantity_updated_successfully!'));
            return back();
        } else {
            Toastr::warning(\App\CPU\translate('product_quantity_can_not_be_less_than_0_!'));
            return back();
        }
    }

    public function status_update(Request $request)
    {
        $product = Product::where(['id' => $request['id']])->first();
        $success = 1;
        if ($request['status'] == 1) {
            if ($product->added_by == 'seller' && $product->request_status == 0) {
                $success = 0;
            } else {
                $product->status = $request['status'];
            }
        } else {
            $product->status = $request['status'];
        }
        $product->save();
        return response()->json([
            'success' => $success,
        ], 200);
    }
    public function updated_shipping(Request $request)
    {
    
        $product = Product::where(['id' => $request['product_id']])->first();
        if($request->status == 1)
        {
            $product->shipping_cost = $product->temp_shipping_cost;
            $product->is_shipping_cost_updated = $request->status;
        }else{
            $product->is_shipping_cost_updated = $request->status;
        }

        $product->save();
        return response()->json([
            
        ], 200);
    }

    public function get_categories(Request $request)
    {
        $cat = Category::where(['parent_id' => $request->parent_id])->get();
        $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($cat as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'select_tag' => $res,
        ]);
    }

    public function sku_combination(Request $request)
    {
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name[array_search('en', $request->lang)];

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $combinations = Helpers::combinations($options);
        return response()->json([
            'view' => view('admin-views.product.partials._sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'))->render(),
        ]);
    }

    public function get_variations(Request $request)
    {
        $product = Product::find($request['id']);
        return response()->json([
            'view' => view('admin-views.product.partials._update_stock', compact('product'))->render()
        ]);
    }

    public function edit($id)
    {
        $product = Product::withoutGlobalScopes()->with('translations')->find($id);
        $product_category = json_decode($product->category_ids);
        $product->colors = json_decode($product->colors);
        $categories = Category::where(['parent_id' => 0])->get();
        $br = Brand::orderBY('name', 'ASC')->get();
        $productVariations = DB::table('product_variations')->where('productId',$id)->get();
        // dd($productVariations);
        return view('admin-views.product2.edit', compact('categories','productVariations', 'br', 'product', 'product_category'));
    }

    public function update(Request $request, $id)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
        ], [
            'name.required' => 'Product name is required!',
            'category_id.required' => 'category  is required!',
            'brand_id.required' => 'brand  is required!',
        ]);

        
        $variations = [];
        $_variations = [];
        $productOptionsNames = [] ;
        foreach($request['prdoctPrice'] as $option){
            if($option['unit'] && $option['unit_price'] && $option['purchase_price']  && $option['numberOfPieces'] ){
    
                if ($option['discount_type'] == 'percent') {
                    $option['dis'] = ($option['unit_price'] / 100) * $option['discount'];
                } else {
                    $option['dis'] = $option['discount'];
                }
        
                if ($option['unit_price'] <= $option['dis']) {
                    $validator->after(function ($validator) {
                        $validator->errors()->add(
                            'unit_price', 'Discount can not be more or equal to the price!'
                        );
                    });
                }
                
                $_option = [
                    'order' => $option['order'],
                    'type' => $option['unit'],
                    'price' => $option['unit_price'],
                    'qty' => 0,
                    'sku' => null,
                    'purchase_price' => $option['purchase_price'],
                    'tax' => $option['tax'],
                    'discount' => $option['discount'],
                    'discount_type' => $option['discount_type'],
                    'tax_type' => $option['tax_type'],
                    'shipping_cost' => $option['shipping_cost'],
                    'multiply_qty' => isset($option['multiplyQTY']) ? $option['multiplyQTY'] : 0,
                    'numberOfPieces' =>  $option['numberOfPieces'],
                    'description' =>  $option['description'],
                ];
                
                $_variations[$option['order']] = $_option;
                // $productOptionsNames[] = $_option['type'];
            }
        }

        if(count($_variations) == 0){
            $validator->after(function ($validator) {
                $validator->errors()->add('options', 'Product can not have one unit');
            });
        }
        
        
        
        
        sort($_variations);
        foreach($_variations as $_variation){
                $variations[] = $_variation;
                $productOptionsNames[] = $_variation['type'];
        }
        
        unset($_variations);

        
        $choice_options = [];
        array_push($choice_options, [
            "name" => "choice_4",
            "title" => "الكمية",
            "options" => $productOptionsNames
        ]);
        


        $product = Product::find($id);
        $product->name = $request->name[array_search('en', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }
        $product->category_ids = json_encode($category);
        $product->brand_id = $request->brand_id;
        $product->unit = $request->unit;
        $product->details = $request->description[array_search('en', $request->lang)];
        $product_images = json_decode($product->images);

        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $product->colors = json_encode($request->colors);
        } else {
            $colors = [];
            $product->colors = json_encode($colors);
        }
        
        
        
        $product->choice_options = json_encode($choice_options);
       
       
        
        $stock_count = (integer)$request['current_stock'];

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)
                ->withInput();
        }

        //combinations end
        $product->variation = json_encode($variations);
        $product->unit_price = BackEndHelper::currency_to_usd($request->unit_price);
        $product->purchase_price = BackEndHelper::currency_to_usd($request->purchase_price);
        $product->tax = $request->tax == 'flat' ? BackEndHelper::currency_to_usd($request->tax) : $request->tax;
        $product->tax_type = $request->tax_type;
        $product->discount = $request->discount_type == 'flat' ? BackEndHelper::currency_to_usd($request->discount) : $request->discount;
        $product->attributes = json_encode($request->choice_attributes);
        $product->discount_type = $request->discount_type;
        $product->current_stock = abs($stock_count);

        $product->video_provider = 'youtube';
        $product->video_url = $request->video_link;
        if ($product->added_by == 'seller' && $product->request_status == 2) {
            $product->request_status = 1;
        }
        $product->shipping_cost = BackEndHelper::currency_to_usd($request->shipping_cost);
        $product->multiply_qty = $request->multiplyQTY=='on'?1:0;
        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('product/', 'png', $img);
                }
                $product->images = json_encode($product_images);
            }

            if ($request->file('image')) {
                $product->thumbnail = ImageManager::update('product/thumbnail/', $product->thumbnail, 'png', $request->file('image'));
            }

            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            if ($request->file('meta_image')) {
                $product->meta_image = ImageManager::update('product/meta/', $product->meta_image, 'png', $request->file('meta_image'));
            }

            $product->save();

            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        ['translationable_type' => 'App\Model\Product',
                            'translationable_id' => $product->id,
                            'locale' => $key,
                            'key' => 'name'],
                        ['value' => $request->name[$index]]
                    );
                }
                if ($request->description[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        ['translationable_type' => 'App\Model\Product',
                            'translationable_id' => $product->id,
                            'locale' => $key,
                            'key' => 'description'],
                        ['value' => $request->description[$index]]
                    );
                }
            }
            
            
            DB::table('product_variations')->where('productId',$product->id)->delete();
            foreach($variations as $variation){
                $variation['productId'] = $product->id;
                DB::table('product_variations')->insert($variation);
            }
            Toastr::success('Product updated successfully.');
            return back();
        }
    }

    public function remove_image(Request $request)
    {
        ImageManager::delete('/product/' . $request['image']);
        $product = Product::find($request['id']);
        $array = [];
        if (count(json_decode($product['images'])) < 2) {
            Toastr::warning('You cannot delete all images!');
            return back();
        }
        foreach (json_decode($product['images']) as $image) {
            if ($image != $request['name']) {
                array_push($array, $image);
            }
        }
        Product::where('id', $request['id'])->update([
            'images' => json_encode($array),
        ]);
        Toastr::success('Product image removed successfully!');
        return back();
    }

    public function delete($id)
    {
        $product = Product::find($id);

        $translation = Translation::where('translationable_type', 'App\Model\Product')
            ->where('translationable_id', $id);
        $translation->delete();

        Cart::where('product_id', $product->id)->delete();
        Wishlist::where('product_id', $product->id)->delete();

        foreach (json_decode($product['images'], true) as $image) {
            ImageManager::delete('/product/' . $image);
        }
        ImageManager::delete('/product/thumbnail/' . $product['thumbnail']);
        $product->delete();

        FlashDealProduct::where(['product_id' => $id])->delete();
        DealOfTheDay::where(['product_id' => $id])->delete();

        Toastr::success('Product removed successfully!');
        return back();
    }

    public function bulk_import_index()
    {
        return view('admin-views.product.bulk-import');
    }

    public function bulk_import_data_old(Request $request)
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error('You have uploaded a wrong format file, please upload the right file.');
            return back();
        }

        
        $data = [];
        $skip = ['youtube_video_url', 'details', 'thumbnail'];
        foreach ($collections as $collection) {
            foreach ($collection as $key => $value) {
                if ($key!="" && $value === "" && !in_array($key, $skip)) {
                    Toastr::error('Please fill ' . $key . ' fields');
                    return back();
                }
            }

            $thumbnail = explode('/', $collection['thumbnail']);

            array_push($data, [
                'name' => $collection['name'],
                'slug' => Str::slug($collection['name'], '-') . '-' . Str::random(6),
                'category_ids' => json_encode([['id' => (string)$collection['category_id'], 'position' => 1], ['id' => (string)$collection['sub_category_id'], 'position' => 2], ['id' => (string)$collection['sub_sub_category_id'], 'position' => 3]]),
                'brand_id' => $collection['brand_id'],
                'unit' => $collection['unit'],
                'min_qty' => $collection['min_qty'],
                'refundable' => $collection['refundable'],
                'unit_price' => $collection['unit_price'],
                'purchase_price' => $collection['purchase_price'],
                'tax' => $collection['tax'],
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'current_stock' => $collection['current_stock'],
                'details' => $collection['details'],
                'video_provider' => 'youtube',
                'video_url' => $collection['youtube_video_url'],
                'images' => json_encode(['def.png']),
                'thumbnail' => $thumbnail[1]??$thumbnail[0],
                'status' => 1,
                'request_status' => 1,
                'colors' => json_encode([]),
                'attributes' => json_encode([]),
                'choice_options' => json_encode([]),
                'variation' => json_encode([]),
                'featured_status' => 1,
                'added_by' => 'admin',
                'user_id' => auth('admin')->id(),
            ]);
        }
        DB::table('products')->insert($data);
        Toastr::success(count($data) . ' - Products imported successfully!');
        return back();
    }

    public function bulk_import_data(Request $request)
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error('You have uploaded a wrong format file, please upload the right file.');
            return back();
        }

        
        $data = [];
        $skip = ['youtube_video_url', 'details', 'thumbnail' , 'id' , 'variation Id' , 'tax_type' , 'shipping_cost' , 'discount'];
        
        $skipInsertAndDelete = [];
        $newProduct = [];
        $variation= []; 
        $_variations= []; 
        $variationsNames= []; 
        foreach ($collections as $collection) {
            foreach ($collection as $key => $value) {
                // id != "" true , "" === "" true , true
                if ($key!="" && $value === "" && !in_array($key, $skip)) {
                    Toastr::error('Please fill ' . $key . ' fields');
                    return back();
                }
            }

            $thumbnail = explode('/', $collection['thumbnail']);
            
            $product = [
                'name' => $collection['name'],
                'slug' => Str::slug($collection['name'], '-') . '-' . Str::random(6),
                'category_ids' => json_encode([['id' => (string)$collection['category_id'], 'position' => 1], ['id' => (string)$collection['sub_category_id'], 'position' => 2], ['id' => (string)$collection['sub_sub_category_id'], 'position' => 3]]),
                'brand_id' => $collection['brand_id'],
                'min_qty' => $collection['min_qty'],
                'refundable' => $collection['refundable'],
                'current_stock' => $collection['current_stock'],
                'details' => $collection['details'],
                'video_provider' => 'youtube',
                'video_url' => $collection['youtube_video_url'],
                'images' => json_encode(['def.png']),
                'thumbnail' => $thumbnail[1]??$thumbnail[0],
                'status' => 1,
                'request_status' => 1,
                'colors' => json_encode([]),
                'attributes' => json_encode([]),
                'choice_options' => json_encode([]),
                'variation' => json_encode([]),
                'featured_status' => 1,
                'added_by' => 'admin',
                'user_id' => auth('admin')->id(),
            ];
            
        
            $_option = [
                'type' => $collection['variation Name'],
                'price' => $collection['unit_price'],
                // 'qty' => 0,
                'qty' => intval($collection['current_stock'] / $collection['number Of Pieces']),
                'sku' => null,
                'purchase_price' => $collection['purchase_price']."",
                'tax' => $collection['tax']."",
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'tax_type' => $collection['tax_type'],
                'shipping_cost' => $collection['shipping_cost']."",
                'multiply_qty' => isset($collection['multiplyQTY']) ? $option['multiplyQTY'] : 0,
                'numberOfPieces' =>  $collection['number Of Pieces']."",
                'description' =>  $collection['variations description'],
                'order' =>  $collection['Sort'],
            ];
            
            if($collection['id']){
                // update 
                if(!isset($skipInsertAndDelete[$collection['id']])){
                    DB::table('products')->where('id' , $collection['id'])->update($product);
                    DB::table('product_variations')->where('productId',$collection['id'])->delete();
                    $_variations[$collection['id']] = [];
                }
                $id = $collection['id'];
                $skipInsertAndDelete[$collection['id']] = 1;
            }
            else{
                if(!isset($newProduct[$collection['name']])){
                    
                    $id = DB::table('products')->insertGetId($product);
                    $newProduct[$collection['name']] = $id;
                }
                else{
                    $id = $newProduct[$collection['name']];
                }
                
            }
            $_variations[$id][intval($collection['Sort'])] = $_option;
            // $variationsNames[$id][] = $collection['variation Name'];
            
            
            
        }
        
        
        
        foreach($_variations as $productId => &$variationProduct){
            ksort($_variations[$productId]);
            foreach($_variations[$productId] as $product_variation){
                $variations[$productId][] = $product_variation;
                $variationsNames[$productId][] = $product_variation['type'];
            }
            
        }
        
        
        // dd($variations);
        foreach($variations as $productId => $variationProduct){
            
            $choice_options = [];
            array_push($choice_options, [
                "name" => "choice_4",
                "title" => "الكمية",
                "options" => $variationsNames[$productId]
            ]);
            DB::table('products')->where('id' , $productId)->update(
                [
                    "variation" =>    json_encode($variationProduct),
                    "choice_options" =>  json_encode($choice_options),  
                ]
            );
            
            
            foreach($variationProduct as $variation){
                $variation['productId'] = $productId;
                $id = DB::table('product_variations')->insertGetId($variation);
            }
                
        
        
        
        }
        Toastr::success(count($data) . ' - Products imported successfully!');
        return back();
    }

    public function bulk_export_data()
    {
        $products = Product::
            select(DB::raw("products.* , 
            
                product_variations.`id` as product_variations_id,product_variations.`productId` as product_variations_productId,product_variations.`type` as product_variations_type,
                product_variations.`price` as product_variations_price,product_variations.`sku` as product_variations_sku,
                product_variations.`purchase_price` as product_variations_purchase_price,product_variations.`tax` as product_variations_tax,
                product_variations.`discount` as product_variations_discount,product_variations.`discount_type` as product_variations_discount_type,
                product_variations.`tax_type` as product_variations_tax_type,product_variations.`shipping_cost` as product_variations_shipping_cost,
                product_variations.`multiply_qty` as product_variations_multiply_qty,product_variations.`numberOfPieces` as product_variations_numberOfPieces,
                product_variations.`qty` as product_variations_qty,
                product_variations.`description` as product_variations_description,
                product_variations.`order` as product_variations_order 
            "))
            ->join('product_variations', 'productId', '=', 'products.id')
            ->where(['added_by' => 'admin'])->get();
        //export from product
        $storage = [];
        $storage[] = [
            'id' => 'id',
            'variationId' => 'variation Id',
            'name' => 'name',
            'category_id' => 'category_id',
            'sub_category_id' => 'sub_category_id',
            'sub_sub_category_id' => 'sub_sub_category_id',
            'brand_id' => 'brand_id',
            // 'unit' => 'unit',
            'min_qty' => 'min_qty',
            'refundable' => 'refundable',
            'youtube_video_url' => 'youtube_video_url',
            'current_stock' => 'current_stock',
            'details' => 'details',
            'thumbnail' => 'thumbnail',
            'sort' => 'Sort',
            'variationName' => 'variation Name',
            'product_variations_numberOfPieces' => 'number Of Pieces',
            'unit_price' => 'unit_price',
            'purchase_price' => 'purchase_price',
            'tax' => 'tax',
            'discount' => 'discount',
            'discount_type' => 'discount_type',
            'tax_type' => 'tax_type',
            'shipping_cost' => 'shipping_cost',
            'product_variations_description' => 'variations description',
        ];
        foreach ($products as $item) {
            $category_id = 0;
            $sub_category_id = 0;
            $sub_sub_category_id = 0;
            foreach (json_decode($item->category_ids, true) as $category) {
                if ($category['position'] == 1) {
                    $category_id = $category['id'];
                } else if ($category['position'] == 2) {
                    $sub_category_id = $category['id'];
                } else if ($category['position'] == 3) {
                    $sub_sub_category_id = $category['id'];
                }
            }
            $storage[] = [
                'id' => $item->id,
                'variationId' => $item->product_variations_id,
                'name' => $item->name,
                'category_id' => $category_id,
                'sub_category_id' => $sub_category_id,
                'sub_sub_category_id' => $sub_sub_category_id,
                'brand_id' => $item->brand_id,
                // 'unit' => $item->unit,
                'min_qty' => $item->min_qty,
                'refundable' => $item->refundable,
                'youtube_video_url' => $item->video_url,
                'current_stock' => $item->current_stock,
                'details' => $item->details,
                'thumbnail' => 'thumbnail/' . $item->thumbnail,
                'sort' => $item->product_variations_order,
                'variationName' => $item->product_variations_type,
                'product_variations_numberOfPieces' => $item->product_variations_numberOfPieces,
                'unit_price' => $item->product_variations_price,
                'purchase_price' => $item->product_variations_purchase_price,
                'tax' => $item->product_variations_tax,
                'discount' => $item->product_variations_discount,
                'discount_type' => $item->product_variations_discount_type,
                'tax_type' => $item->product_variations_tax_type,
                'shipping_cost' => $item->product_variations_shipping_cost,
                'product_variations_description' => $item->product_variations_description,
            ];
        }
        // return (new FastExcel($storage))->download('inhouse_products.xlsx');
        SimpleXLSXGen::fromArray( $storage )->downloadAs('inhouse_products.xlsx');
    }
}
