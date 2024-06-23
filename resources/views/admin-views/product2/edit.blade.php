@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Product Edit'))

@push('css_or_js')
    <link href="{{asset('public/assets/back-end/css/tags-input.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        #addnewType button.close {
            padding: 0;
            background-color: transparent;
            border: 0;
            -webkit-appearance: none;
        }
        #addnewType .modal-header .close {
            padding: 1rem;
            margin: -1rem -1rem -1rem 0px;
        }
        #addnewType .close:not(:disabled):not(.disabled) {
            cursor: pointer;
        }
        #addnewType .modal-title {
            margin-bottom: 0;
            line-height: 1.5;
        }
        #addnewType .modal-header {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: start;
            -ms-flex-align: start;
            align-items: flex-start;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            border-top-left-radius: 0.3rem;
            border-top-right-radius: 0.3rem;
        }
        
        .inner.show[role="listbox"]{
            max-height: 120px !important;
        }
    </style>
@endpush

@section('content')
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="{{url('public/select/')}}/bootstrap-select-1.13.14/dist/css/bootstrap-select.min.css">
    <!-- Page Heading -->
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{\App\CPU\translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page"><a
                        href="{{route('admin.product.list', ['in_house', ''])}}">{{\App\CPU\translate('Product')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{\App\CPU\translate('Edit')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{route('admin.product.update',$product->id)}}" method="post"
                      style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                      enctype="multipart/form-data"
                      id="product_form">
                    @csrf

                    <div class="card">
                        <div class="card-header">
                            @php($language=\App\Model\BusinessSetting::where('type','pnc_language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')

                            @php($default_lang = json_decode($language)[0])
                            <ul class="nav nav-tabs mb-4">
                                @foreach(json_decode($language) as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#"
                                           id="{{$lang}}-link">{{\App\CPU\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="card-body">
                            @foreach(json_decode($language) as $lang)
                                <?php
                                if (count($product['translations'])) {
                                    $translate = [];
                                    foreach ($product['translations'] as $t) {

                                        if ($t->locale == $lang && $t->key == "name") {
                                            $translate[$lang]['name'] = $t->value;
                                        }
                                        if ($t->locale == $lang && $t->key == "description") {
                                            $translate[$lang]['description'] = $t->value;
                                        }

                                    }
                                }
                                ?>
                                <div class="{{$lang != 'en'? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                    <div class="form-group">
                                        <label class="input-label" for="{{$lang}}_name">{{\App\CPU\translate('name')}}
                                            ({{strtoupper($lang)}})</label>
                                        <input type="text" {{$lang == 'en'? 'required':''}} name="name[]"
                                               id="{{$lang}}_name"
                                               value="{{$translate[$lang]['name']??$product['name']}}"
                                               class="form-control" placeholder="{{\App\CPU\translate('New Product')}}" required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                    <div class="form-group pt-4">
                                        <label class="input-label">{{\App\CPU\translate('description')}}
                                            ({{strtoupper($lang)}})</label>
                                        <textarea name="description[]" style="display:none" class="textarea"
                                                  required>{!! $translate[$lang]['description']??$product['details'] !!}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>{{\App\CPU\translate('General Info')}}</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="name">{{\App\CPU\translate('Category')}}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="category_id"
                                            id="category_id"
                                            onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-category-select','select')">
                                            <option value="0" selected disabled>---{{\App\CPU\translate('Select')}}---</option>
                                            @foreach($categories as $category)
                                                <option
                                                    value="{{$category['id']}}" {{ $category->id==$product_category[0]->id ? 'selected' : ''}} >{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="name">{{\App\CPU\translate('Sub Category')}}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="sub_category_id" id="sub-category-select"
                                            data-id="{{count($product_category)>=2?$product_category[1]->id:''}}"
                                            onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-sub-category-select','select')">
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="name">{{\App\CPU\translate('Sub Sub Category')}}</label>

                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            data-id="{{count($product_category)>=3?$product_category[2]->id:''}}"
                                            name="sub_sub_category_id" id="sub-sub-category-select">

                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name">{{\App\CPU\translate('Brand')}}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="brand_id">
                                            <option value="{{null}}" selected disabled>---{{\App\CPU\translate('Select')}}---</option>
                                            @foreach($br as $b)
                                                <option
                                                    value="{{$b['id']}}" {{ $b->id==$product->brand_id ? 'selected' : ''}} >{{$b['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-6 col-md-6 col-lg-4" id="quantity">
                                        <label
                                            class="control-label">{{\App\CPU\translate('total')}} {{\App\CPU\translate('Quantity')}}</label>
                                        <input type="number" min="0" step="1"
                                               placeholder="{{\App\CPU\translate('Quantity')}}"
                                               value="{{$product->current_stock}}" 
                                               name="current_stock" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>{{\App\CPU\translate('Variation')}}</h4>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">

                                        <label for="colors">
                                            {{\App\CPU\translate('Colors')}} :
                                        </label>
                                        <label class="switch">
                                            <input type="checkbox" class="status"
                                                   name="colors_active" {{count($product['colors'])>0?'checked':''}}>
                                            <span class="slider round"></span>
                                        </label>

                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control color-var-select"
                                            name="colors[]" multiple="multiple"
                                            id="colors-selector" {{count($product['colors'])>0?'':'disabled'}}>
                                            @foreach (\App\Model\Color::orderBy('name', 'asc')->get() as $key => $color)
                                                <option
                                                    value={{ $color->code }} {{in_array($color->code,$product['colors'])?'selected':''}}>
                                                    {{$color['name']}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="attributes" style="padding-bottom: 3px">
                                            {{\App\CPU\translate('Attributes')}} :
                                        </label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="choice_attributes[]" id="choice_attributes" multiple="multiple">
                                            @foreach (\App\Model\Attribute::orderBy('name', 'asc')->get() as $key => $a)
                                                @if($product['attributes']!='null')
                                                    <option
                                                        value="{{ $a['id']}}" {{in_array($a->id,json_decode($product['attributes'],true))?'selected':''}}>
                                                        {{$a['name']}}
                                                    </option>
                                                @else
                                                    <option value="{{ $a['id']}}">{{$a['name']}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mt-2 mb-2">
                                        <div class="customer_choice_options" id="customer_choice_options">
                                            @include('admin-views.product.partials._choices',['choice_no'=>json_decode($product['attributes']),'choice_options'=>json_decode($product['choice_options'],true)])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>{{\App\CPU\translate('Product price & stock')}}</h4>
                        </div>
                        <div class=" price-list">
                            
                            
                        @foreach($productVariations as $index => $Variation)
                            <div class="card-body item-price">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="control-label">{{\App\CPU\translate('Sort')}}</label>
                                            <input type="number" min="1" step="1"
                                                    name="prdoctPrice[{{$index}}][order]"
                                                    value="{{isset($Variation->order) ? $Variation->order : 0}}"
                                                    class="form-control"
                                                    required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="name">{{\App\CPU\translate('Unit')}}</label>
                                            <div class="col-12 row">
                                                
                                                
                                                <select
                                                    class="col-10 js-example-basic-multiple form-control select-unit"
                                                    data-live-search="true"
                                                    name="prdoctPrice[{{$index}}][unit]" >
                                                    @foreach(\App\CPU\Helpers::units() as $x)
                                                        <option
                                                            value="{{$x}}" {{$Variation->type==$x? 'selected':''}}>{{$x}}</option>
                                                    @endforeach
                                                </select>
                                                {{-- <button class="btn btn-primary"></button> --}}
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addnewType">+</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">{{\App\CPU\translate('Unit price')}}</label>
                                            <input type="number" min="0" step="0.01"
                                                   placeholder="{{\App\CPU\translate('Unit price')}}"
                                                   name="prdoctPrice[{{$index}}][unit_price]" value="{{$Variation->price}}" class="form-control"
                                                   required>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-4" id="numberOfPieces">
                                            <label
                                                class="control-label">{{\App\CPU\translate('numberOfPieces')}}</label>
                                            <input type="number" min="1"  step="1"
                                                   placeholder="{{\App\CPU\translate('Quantity')}}"
                                                   name="prdoctPrice[{{$index}}][numberOfPieces]" 
                                                   value="{{$Variation->numberOfPieces}}"
                                                   class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label
                                                class="control-label">{{\App\CPU\translate('Purchase price')}}</label>
                                            <input type="number" min="0" step="0.01"
                                                   placeholder="{{\App\CPU\translate('Purchase price')}}"
                                                   value="{{$Variation->purchase_price}}"
                                                   name="prdoctPrice[{{$index}}][purchase_price]" class="form-control" required>
                                        </div>
                                        
                                        <div class="col-md-5">
                                            <label class="control-label">{{\App\CPU\translate('Tax')}}</label>
                                            <label class="badge badge-info">{{\App\CPU\translate('Percent')}} ( % )</label>
                                            <input type="number" min="0"  step="0.01"
                                                   placeholder="{{\App\CPU\translate('Tax')}}}" name="prdoctPrice[{{$index}}][tax]"
                                                   value="{{$Variation->tax}}"
                                                   class="form-control">
                                            <input name="prdoctPrice[{{$index}}][tax_type]" value="percent" style="display: none">
                                        </div>
    
                                        <div class="col-md-5">
                                            <label class="control-label">{{\App\CPU\translate('Discount')}}</label>
                                            <input type="number" min="0"  step="0.01"
                                                   placeholder="{{\App\CPU\translate('Discount')}}" name="prdoctPrice[{{$index}}][discount]"
                                                   value="{{$Variation->discount}}"
                                                   class="form-control" required>
                                        </div>
                                        <div class="col-md-2" style="padding-top: 30px;">
                                            <select style="width: 100%"
                                                class="js-example-basic-multiple js-states js-example-responsive demo-select2"
                                                name="prdoctPrice[{{$index}}][discount_type]">
                                                <option {{ $Variation->discount_type == "flat" ? "selected" : "" }} value="flat">{{\App\CPU\translate('Flat')}}</option>
                                                <option {{ $Variation->discount_type == "percent" ? "selected" : "" }} value="percent">{{\App\CPU\translate('Percent')}}</option>
                                            </select>
                                        </div>
                                        {{-- <div class="pt-4 col-12 sku_combination" id="sku_combination">
    
                                        </div> --}}
                                        
                                        <div class="col-sm-12 col-md-12 col-lg-12" id="shipping_cost">
                                            <label
                                                class="control-label">{{\App\CPU\translate('description')}} </label>
                                            <textarea placeholder="{{\App\CPU\translate('description')}}"
                                                   value="{{$Variation->description}}"
                                                   name="prdoctPrice[{{$index}}][description]" class="form-control" required>{{$Variation->description}}</textarea>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-4" id="shipping_cost">
                                            <label
                                                class="control-label">{{\App\CPU\translate('shipping_cost')}} </label>
                                            <input type="number" min="0" step="1"
                                                   placeholder="{{\App\CPU\translate('shipping_cost')}}"
                                                   value="{{$Variation->shipping_cost}}"
                                                   name="prdoctPrice[{{$index}}][shipping_cost]" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 col-lg-4 mt-sm-1" id="shipping_cost_multy">
                                            <div>
                                                <label
                                                class="control-label">{{\App\CPU\translate('shipping_cost_multiply_with_quantity')}} </label>
                                            
                                            </div>
                                            <div>
                                                <label class="switch">
                                                    <input type="checkbox" name="prdoctPrice[{{$index}}][multiplyQTY]"
                                                    {{ $Variation->multiply_qty == 1 ? "checked" : "" }}
                                                           id="" >
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button class="btn btn-danger remove-product">Remove</button>
                            </div>
                        @endForeach
                        </div>
                        <div>
                            <button class="btn btn-primary add-more">Add More</button>
                        </div>

                    </div>


                    <div class="card mt-2 mb-2 rest-part">
                        <div class="card-header">
                            <h4>{{\App\CPU\translate('seo_section')}}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="control-label">{{\App\CPU\translate('Meta Title')}}</label>
                                    <input type="text" name="meta_title" value="{{$product['meta_title']}}" placeholder="" class="form-control">
                                </div>

                                <div class="col-md-8 mb-4">
                                    <label class="control-label">{{\App\CPU\translate('Meta Description')}}</label>
                                    <textarea rows="10" type="text" name="meta_description" class="form-control">{{$product['meta_description']}}</textarea>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label>{{\App\CPU\translate('Meta Image')}}</label>
                                    </div>
                                    <div class="border-dashed">
                                        <div class="row" id="meta_img">
                                            <div class="col-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <img style="width: 100%" height="auto"
                                                             onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                             src="{{asset("storage/app/public/product/meta")}}/{{$product['meta_image']}}"
                                                             alt="Meta image">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="control-label">{{\App\CPU\translate('Youtube video link')}}</label>
                                    <small class="badge badge-soft-danger"> ( {{\App\CPU\translate('optional, please provide embed link not direct link')}}. )</small>
                                    <input type="text" value="{{$product['video_url']}}" name="video_link"
                                           placeholder="{{\App\CPU\translate('EX')}} : https://www.youtube.com/embed/5R06LRdUCSE"
                                           class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{\App\CPU\translate('Upload product images')}}</label><small
                                            style="color: red">* ( {{\App\CPU\translate('ratio')}} 1:1 )</small>
                                    </div>
                                    <div class="p-2 border border-dashed" style="max-width:430px;">
                                        <div class="row" id="coba">
                                            @foreach (json_decode($product->images) as $key => $photo)
                                                <div class="col-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <img style="width: 100%" height="auto"
                                                                 onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                                 src="{{asset("storage/app/public/product/$photo")}}"
                                                                 alt="Product image">
                                                            <a href="{{route('admin.product.remove-image',['id'=>$product['id'],'name'=>$photo])}}"
                                                               class="btn btn-danger btn-block">{{\App\CPU\translate('Remove')}}</a>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{\App\CPU\translate('Upload thumbnail')}}</label><small
                                            style="color: red">* ( {{\App\CPU\translate('ratio')}} 1:1 )</small>
                                    </div>

                                    <div class="row" id="thumbnail">
                                        <div class="col-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <img style="width: 100%" height="auto"
                                                         onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                         src="{{asset("storage/app/public/product/thumbnail")}}/{{$product['thumbnail']}}"
                                                         alt="Product image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-footer">
                        <div class="row">
                            <div class="col-md-12" style="padding-top: 20px">
                                @if($product->request_status == 2)
                                    <button type="button" onclick="check()" class="btn btn-primary">{{\App\CPU\translate('Update & Publish')}}</button>
                                @else
                                    <button type="button" onclick="check()" class="btn btn-primary float-right">{{\App\CPU\translate('Update')}}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        
        <div class="modal " id="addnewType" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{\App\CPU\translate('Add')}} {{\App\CPU\translate('Unit')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form class="col-12" id="unitForm">
                            <div class="form-group">
                                <label for="exampleInputEmail1">{{\App\CPU\translate('unit')}}</label>
                                <input type="hidden" class="form-control" name="_token" id="unit_name" value="{{ csrf_token() }}">
                                <input type="text" class="form-control" name="name" id="unit_name">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button id="btn-unitForm" type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('public/assets/back-end')}}/js/tags-input.min.js"></script>
    <script src="{{asset('public/assets/back-end/js/spartan-multi-image-picker.js')}}"></script>
    
    
    <!-- Latest compiled and minified JavaScript -->
    <script src="{{url('public/select/')}}/bootstrap-select-1.13.14/dist/js/bootstrap-select.min.js"></script>
    
    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <script src="{{url('public/select/')}}/bootstrap-select-1.13.14/dist/js/i18n/defaults-en_US.js"></script>
    <script>
    
        
        $(document).ready(function(){
            $('.select-unit').selectpicker();
        })
        
        var index_list = <?=count($productVariations)?>; 
        $('body').on('click' ,'.remove-product', function(e){
            $(this).parents('.item-price').remove();
        });
        $('.add-more').on('click' , function(e){
            index_list += 1 ;
            e.preventDefault();
            $('.price-list').append(
                `
                    <div class="card-body item-price">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="control-label">{{\App\CPU\translate('Sort')}}</label>
                                    <input type="number" min="1" step="1"
                                            name="prdoctPrice[`+index_list+`][order]" class="form-control"
                                            required>
                                </div>
                                <div class="col-md-6">
                                    <label for="name">{{\App\CPU\translate('Unit')}}</label>
                                    <div class="col-12 row">
                                        <select
                                            class="col-10 js-example-basic-multiple form-control select-unit"
                                            data-live-search="true"
                                            name="prdoctPrice[`+index_list+`][unit]" >
                                            @foreach(\App\CPU\Helpers::units() as $x)
                                                <option
                                                    value="{{$x}}" {{old('unit')==$x? 'selected':''}}>{{$x}}</option>
                                            @endforeach
                                        </select>
                                        {{-- <button class="btn btn-primary"></button> --}}
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addnewType">+</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label">{{\App\CPU\translate('Unit price')}}</label>
                                    <input type="number" min="0" step="0.01"
                                            placeholder="{{\App\CPU\translate('Unit price')}}"
                                            name="prdoctPrice[`+index_list+`][unit_price]" value="{{old('unit_price')}}" class="form-control"
                                            required>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-4" id="quantity">
                                    <label
                                        class="control-label">{{\App\CPU\translate('numberOfPieces')}}</label>
                                    <input type="number" min="1" value="1" step="1"
                                            placeholder="{{\App\CPU\translate('numberOfPieces')}}"
                                            name="prdoctPrice[`+index_list+`][numberOfPieces]" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label
                                        class="control-label">{{\App\CPU\translate('Purchase price')}}</label>
                                    <input type="number" min="0" step="0.01"
                                            placeholder="{{\App\CPU\translate('Purchase price')}}"
                                            value="{{old('purchase_price')}}"
                                            name="prdoctPrice[`+index_list+`][purchase_price]" class="form-control" required>
                                </div>
                                
                                <div class="col-md-5">
                                    <label class="control-label">{{\App\CPU\translate('Tax')}}</label>
                                    <label class="badge badge-info">{{\App\CPU\translate('Percent')}} ( % )</label>
                                    <input type="number" min="0" value="0" step="0.01"
                                            placeholder="{{\App\CPU\translate('Tax')}}}" name="prdoctPrice[`+index_list+`][tax]"
                                            value="{{old('tax')}}"
                                            class="form-control">
                                    <input name="prdoctPrice[`+index_list+`][tax_type]" value="percent" style="display: none">
                                </div>

                                <div class="col-md-5">
                                    <label class="control-label">{{\App\CPU\translate('Discount')}}</label>
                                    <input type="number" min="0" value="{{old('discount')}}" step="0.01"
                                            placeholder="{{\App\CPU\translate('Discount')}}" name="prdoctPrice[`+index_list+`][discount]"
                                            class="form-control" required>
                                </div>
                                <div class="col-md-2" style="padding-top: 30px;">
                                    <select style="width: 100%"
                                        class="js-example-basic-multiple js-states js-example-responsive demo-select2"
                                        name="prdoctPrice[`+index_list+`][discount_type]">
                                        <option value="flat">{{\App\CPU\translate('Flat')}}</option>
                                        <option value="percent">{{\App\CPU\translate('Percent')}}</option>
                                    </select>
                                </div>
                                {{-- <div class="pt-4 col-12 sku_combination" id="sku_combination">

                                </div> --}}
                                
                                <div class="col-sm-12 col-md-12 col-lg-12" id="shipping_cost">
                                    <label
                                        class="control-label">{{\App\CPU\translate('description')}} </label>
                                    <textarea placeholder="{{\App\CPU\translate('description')}}"
                                           name="prdoctPrice[`+index_list+`][description]" class="form-control" required></textarea>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-4" id="shipping_cost">
                                    <label
                                        class="control-label">{{\App\CPU\translate('shipping_cost')}} </label>
                                    <input type="number" min="0" value="0" step="1"
                                            placeholder="{{\App\CPU\translate('shipping_cost')}}"
                                            name="prdoctPrice[`+index_list+`][shipping_cost]" class="form-control" required>
                                </div>
                                <div class="col-md-6 col-lg-4 mt-sm-1" id="shipping_cost_multy">
                                    <div>
                                        <label
                                        class="control-label">{{\App\CPU\translate('shipping_cost_multiply_with_quantity')}} </label>
                                    
                                    </div>
                                    <div>
                                        <label class="switch">
                                            <input type="checkbox" name="prdoctPrice[`+index_list+`][multiplyQTY]"
                                                    id="" >
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-danger remove-product">Remove</button>
                    </div>
                `
            )
            $('.select-unit').selectpicker();
        })
    
        
        $('#btn-unitForm').on('click',function(e){
            e.preventDefault();
            $.ajax({
                type: "post",
                url: "{{route('admin.product.add-type')}}",
                data:$('#unitForm').serialize(), 
                dataType: "json",
                success: function (response) {
                    var name = $('#unitForm [name="name"]').val();
                    if(response.status){
                        $('select.select-unit').append(`<option value="`+name+`">`+name+`</option>`);
                        $('select.select-unit').selectpicker('refresh');
                    }
                    $('#addnewType').modal('hide');

                }
            });
        })
        $('body').on('click','[data-target="#addnewType"]',function(e){
            e.preventDefault();
            $('#addnewType').modal('show');
        });
    
        var imageCount = {{10-count(json_decode($product->images))}};
        var thumbnail = '{{\App\CPU\ProductManager::product_image_path('thumbnail').'/'.$product->thumbnail??asset('public/assets/back-end/img/400x400/img2.jpg')}}';
        $(function () {
            if (imageCount > 0) {
                $("#coba").spartanMultiImagePicker({
                    fieldName: 'images[]',
                    maxCount: imageCount,
                    rowHeight: 'auto',
                    groupClassName: 'col-6',
                    maxFileSize: '',
                    placeholderImage: {
                        image: '{{asset('public/assets/back-end/img/400x400/img2.jpg')}}',
                        width: '100%',
                    },
                    dropFileLabel: "Drop Here",
                    onAddRow: function (index, file) {

                    },
                    onRenderedPreview: function (index) {

                    },
                    onRemoveRow: function (index) {

                    },
                    onExtensionErr: function (index, file) {
                        toastr.error('{{\App\CPU\translate('Please only input png or jpg type file')}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    },
                    onSizeErr: function (index, file) {
                        toastr.error('{{\App\CPU\translate('File size too big')}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                });
            }

            $("#thumbnail").spartanMultiImagePicker({
                fieldName: 'image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-6',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('public/assets/back-end/img/400x400/img2.jpg')}}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{\App\CPU\translate('Please only input png or jpg type file')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{\App\CPU\translate('File size too big')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

            $("#meta_img").spartanMultiImagePicker({
                fieldName: 'meta_image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-6',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('public/assets/back-end/img/400x400/img2.jpg')}}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{\App\CPU\translate('Please only input png or jpg type file')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{\App\CPU\translate('File size too big')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function () {
            readURL(this);
        });

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>

    <script>
        function getRequest(route, id, type) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    if (type == 'select') {
                        $('#' + id).empty().append(data.select_tag);
                    }
                },
            });
        }

        $('input[name="colors_active"]').on('change', function () {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors-selector').prop('disabled', true);
            } else {
                $('#colors-selector').prop('disabled', false);
            }
        });

        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                //console.log($(this).val());
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append('<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="{{\App\CPU\translate('Choice Title') }}" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="{{\App\CPU\translate('Enter choice values') }}" data-role="tagsinput" onchange="update_sku()"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        setTimeout(function () {
            $('.call-update-sku').on('change', function () {
                update_sku();
            });
        }, 2000)

        $('#colors-selector').on('change', function () {
            update_sku();
        });

        $('input[name="unit_price"]').on('keyup', function () {
            update_sku();
        });

        function update_sku() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('admin.product.sku-combination')}}',
                data: $('#product_form').serialize(),
                success: function (data) {
                    $('#sku_combination').html(data.view);
                    update_qty();
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }

        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category_id").val();
                let sub_category = $("#sub-category-select").attr("data-id");
                let sub_sub_category = $("#sub-sub-category-select").attr("data-id");
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + category + '&sub_category=' + sub_category, 'sub-category-select', 'select');
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + sub_category + '&sub_category=' + sub_sub_category, 'sub-sub-category-select', 'select');
            }, 100)
            // color select select2
            $('.color-var-select').select2({
                templateResult: colorCodeSelect,
                templateSelection: colorCodeSelect,
                escapeMarkup: function (m) {
                    return m;
                }
            });

            function colorCodeSelect(state) {
                var colorCode = $(state.element).val();
                if (!colorCode) return state.text;
                return "<span class='color-preview' style='background-color:" + colorCode + ";'></span>" + state.text;
            }
        });
    </script>

    <script>
        function check() {
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            var formData = new FormData(document.getElementById('product_form'));
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.product.update',$product->id)}}',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('product updated successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        $('#product_form').submit();
                    }
                }
            });
        };
    </script>

    <script>
        update_qty();

        function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="qty_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if (qty_elements.length > 0) {

                $('input[name="current_stock"]').attr("readonly", true);
                $('input[name="current_stock"]').val(total_qty);
            } else {
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }

        $('input[name^="qty_"]').on('keyup', function () {
            var total_qty = 0;
            var qty_elements = $('input[name^="qty_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            $('input[name="current_stock"]').val(total_qty);
        });
    </script>

    <script>
        $(".lang_link").click(function (e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{$default_lang}}') {
                $(".rest-part").removeClass('d-none');
            } else {
                $(".rest-part").addClass('d-none');
            }
        })
    </script>

    {{--ck editor--}}
    <script src="{{asset('/')}}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{asset('/')}}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('.textarea').ckeditor({
            contentsLangDirection : '{{Session::get('direction')}}',
        });
    </script>
    {{--ck editor--}}
@endpush
