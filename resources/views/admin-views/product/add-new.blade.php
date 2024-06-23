@extends('layouts.back-end.app')

@section('title', translate('product_Add'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">

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
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
                {{ translate('add_New_Product') }}
            </h2>
        </div>

        <form class="product-form text-start" action="{{ route('admin.products.store') }}" method="POST"
              enctype="multipart/form-data" id="product_form">
            @csrf
            <div class="card">
                <div class="px-4 pt-3 d-flex justify-content-between">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        @foreach ($languages as $lang)
                            <li class="nav-item">
                                <span class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer"
                                      id="{{ $lang }}-link">{{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <a class="btn btn--primary btn-sm text-capitalize h-100" href="{{route('admin.products.product-gallery') }}">
                        {{translate('add_info_from_gallery')}}
                    </a>
                </div>

                <div class="card-body">
                    @foreach ($languages as $lang)
                        <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                             id="{{ $lang }}-form">
                            <div class="form-group">
                                <label class="title-color"
                                       for="{{ $lang }}_name">{{ translate('product_name') }}
                                    ({{ strtoupper($lang) }})
                                </label>
                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="name[]"
                                       id="{{ $lang }}_name" class="form-control" placeholder="New Product">
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                            <div class="form-group pt-2">
                                <label class="title-color"
                                       for="{{ $lang }}_description">{{ translate('description') }}
                                    ({{ strtoupper($lang) }})</label>
                                <textarea class="summernote" name="description[]">{{ old('details') }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card mt-2 rest-part">
                <div class="card-header">
                    <h4>{{translate('General Info')}}</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="name">{{translate('Category')}}</label>
                                <select
                                    class="js-example-basic-multiple form-control"
                                    name="category_id"
                                    onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-category-select','select')"
                                    required>
                                    <option value="{{old('category_id')}}" selected disabled>---Select---</option>
                                    @foreach($categories as $c)
                                        <option value="{{$c['id']}}" {{old('name')==$c['id']? 'selected': ''}}>
                                            {{$c['name']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="name">{{translate('Sub Category')}}</label>
                                <select class="js-example-basic-multiple form-control"
                                        name="sub_category_id" id="sub-category-select"
                                        onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-sub-category-select','select')">
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="name">{{translate('Sub Sub Category')}}</label>
                                <select
                                    class="js-example-basic-multiple form-control"
                                    name="sub_sub_category_id" id="sub-sub-category-select">

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="name">{{translate('Brand')}}</label>
                                <select
                                    class="js-example-basic-multiple js-states js-example-responsive form-control"
                                    name="brand_id" required>
                                    <option value="{{null}}" selected disabled>---{{translate('Select')}}---</option>
                                    @foreach($brands as $b)
                                        <option value="{{$b['id']}}">{{$b['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-4" id="quantity">
                                <label
                                    class="control-label">{{translate('total')}} {{translate('Quantity')}}</label>
                                <input type="number" min="0" value="0" step="1"
                                       placeholder="{{translate('Quantity')}}"
                                       name="current_stock" class="form-control" required>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-2 rest-part">
                <div class="card-header">
                    <h4>{{translate('Variations')}}</h4>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="colors">
                                    {{translate('Colors')}} :
                                </label>
                                <label class="switch">
                                    <input type="checkbox" class="status" value="{{old('colors_active')}}"
                                           name="colors_active">
                                    <span class="slider round"></span>
                                </label>
                                <select
                                    class="js-example-basic-multiple js-states js-example-responsive form-control color-var-select"
                                    name="colors[]" multiple="multiple" id="colors-selector" disabled>
                                    @foreach (\App\Models\Color::orderBy('name', 'asc')->get() as $key => $color)
                                        <option value="{{ $color->code }}">
                                            {{$color['name']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="attributes" style="padding-bottom: 3px">
                                    {{translate('Attributes')}} :
                                </label>
                                <select
                                    class="js-example-basic-multiple js-states js-example-responsive form-control"
                                    name="choice_attributes[]" id="choice_attributes" multiple="multiple">
                                    @foreach (\App\Models\Attribute::orderBy('name', 'asc')->get() as $key => $a)
                                        <option value="{{ $a['id']}}">
                                            {{$a['name']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mt-2 mb-2">
                                <div class="customer_choice_options" id="customer_choice_options"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-2 rest-part">
                <div class="card-header">
                    <h4>{{translate('Product price & stock')}}</h4>
                </div>
                <div class=" price-list">
                    <div class="card-body item-price">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="control-label">{{translate('Sort')}}</label>
                                    <input type="number" min="1" step="1"
                                           name="prdoctPrice[0][order]"
                                           class="form-control"
                                           required>
                                </div>
                                <div class="col-md-6">
                                    <label for="name">{{translate('Unit')}}</label>
                                    <div class="col-12 row">
                                        <select
                                            class="col-10 js-example-basic-multiple form-control select-unit"
                                            data-live-search="true"
                                            name="prdoctPrice[0][unit]" >
                                            @foreach(units() as $x)
                                                <option
                                                    value="{{$x}}" {{old('unit')==$x? 'selected':''}}>{{$x}}</option>
                                            @endforeach
                                        </select>
                                        {{-- <button class="btn btn-primary"></button> --}}
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addnewType">+</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label">{{translate('Unit price')}}</label>
                                    <input type="number" min="0" step="0.01"
                                           placeholder="{{translate('Unit price')}}"
                                           name="prdoctPrice[0][unit_price]" value="{{old('unit_price')}}" class="form-control"
                                           required>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-4" id="numberOfPieces">
                                    <label
                                        class="control-label">{{translate('numberOfPieces')}}</label>
                                    <input type="number" min="1" value="1" step="1"
                                           placeholder="{{translate('Quantity')}}"
                                           name="prdoctPrice[0][numberOfPieces]" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label
                                        class="control-label">{{translate('Purchase price')}}</label>
                                    <input type="number" min="0" step="0.01"
                                           placeholder="{{translate('Purchase price')}}"
                                           value="{{old('purchase_price')}}"
                                           name="prdoctPrice[0][purchase_price]" class="form-control" required>
                                </div>

                                <div class="col-md-5">
                                    <label class="control-label">{{translate('Tax')}}</label>
                                    <label class="badge badge-info">{{translate('Percent')}} ( % )</label>
                                    <input type="number" min="0" value="0" step="0.01"
                                           placeholder="{{translate('Tax')}}}" name="prdoctPrice[0][tax]"
                                           value="{{old('tax')}}"
                                           class="form-control">
                                    <input name="prdoctPrice[0][tax_type]" value="percent" style="display: none">
                                </div>

                                <div class="col-md-5">
                                    <label class="control-label">{{translate('Discount')}}</label>
                                    <input type="number" min="0" value="{{old('discount')}}" step="0.01"
                                           placeholder="{{translate('Discount')}}" name="prdoctPrice[0][discount]"
                                           class="form-control" required>
                                </div>
                                <div class="col-md-2" style="padding-top: 30px;">
                                    <select style="width: 100%"
                                            class="js-example-basic-multiple js-states js-example-responsive demo-select2"
                                            name="prdoctPrice[0][discount_type]">
                                        <option value="flat">{{translate('Flat')}}</option>
                                        <option value="percent">{{translate('Percent')}}</option>
                                    </select>
                                </div>
                                {{-- <div class="pt-4 col-12 sku_combination" id="sku_combination">

                                </div> --}}
                                <div class="col-sm-12 col-md-12 col-lg-12" id="shipping_cost">
                                    <label
                                        class="control-label">{{translate('description')}} </label>
                                    <textarea placeholder="{{translate('description')}}"
                                              name="prdoctPrice[0][description]" class="form-control" required></textarea>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-4" id="shipping_cost">
                                    <label
                                        class="control-label">{{translate('shipping_cost')}} </label>
                                    <input type="number" min="0" value="0" step="1"
                                           placeholder="{{translate('shipping_cost')}}"
                                           name="prdoctPrice[0][shipping_cost]" class="form-control" required>
                                </div>
                                <div class="col-md-6 col-lg-4 mt-sm-1" id="shipping_cost_multy">
                                    <div>
                                        <label
                                            class="control-label">{{translate('shipping_cost_multiply_with_quantity')}} </label>

                                    </div>
                                    <div>
                                        <label class="switch">
                                            <input type="checkbox" name="prdoctPrice[0][multiplyQTY]"
                                                   id="" >
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-danger remove-product">Remove</button>
                    </div>
                </div>
                <div>
                    <button class="btn btn-primary add-more">Add More</button>
                </div>

            </div>

            <div class="card mt-2 mb-2 rest-part">
                <div class="card-header">
                    <h4>{{translate('seo_section')}}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="control-label">{{translate('Meta Title')}}</label>
                            <input type="text" name="meta_title" placeholder="" class="form-control">
                        </div>

                        <div class="col-md-8 mb-4">
                            <label class="control-label">{{translate('Meta Description')}}</label>
                            <textarea rows="10" type="text" name="meta_description" class="form-control"></textarea>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label>{{translate('Meta Image')}}</label>
                            </div>
                            <div class="border border-dashed">
                                <div class="row" id="meta_img"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-2 rest-part">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="control-label">{{translate('Youtube video link')}}</label>
                            <small class="badge badge-soft-danger"> ( {{translate('optional, please provide embed link not direct link')}}. )</small>
                            <input type="text" name="video_link" placeholder="{{translate('EX')}} : https://www.youtube.com/embed/5R06LRdUCSE" class="form-control" required>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label>{{translate('Upload product images')}}</label><small
                                    style="color: red">* ( {{translate('ratio')}} 1:1 )</small>
                            </div>
                            <div class="p-2 border border-dashed" style="max-width:430px;">
                                <div class="row" id="coba"></div>
                            </div>

                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">{{translate('Upload thumbnail')}}</label><small
                                    style="color: red">* ( {{translate('ratio')}} 1:1 )</small>
                            </div>
                            <div style="max-width:200px;">
                                <div class="row" id="thumbnail"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-footer">
                <div class="row">
                    <div class="col-md-12" style="padding-top: 20px">
                        <button type="button" onclick="check()" class="btn btn-primary float-right">{{translate('Submit')}}</button>
                    </div>
                </div>
            </div>

{{--            <div class="card mt-3 rest-part">--}}
{{--                <div class="card-header">--}}
{{--                    <div class="d-flex gap-2">--}}
{{--                        <i class="tio-user-big"></i>--}}
{{--                        <h4 class="mb-0">{{ translate('general_setup') }}</h4>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="card-body">--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3">--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="name" class="title-color">{{ translate('category') }}</label>--}}
{{--                                <select class="js-select2-custom form-control action-get-request-onchange" name="category_id"--}}
{{--                                        data-url-prefix="{{ url('/admin/products/get-categories?parent_id=') }}"--}}
{{--                                        data-element-id="sub-category-select"--}}
{{--                                        data-element-type="select"--}}
{{--                                        required>--}}
{{--                                    <option value="{{ old('category_id') }}" selected--}}
{{--                                            disabled>{{ translate('select_category') }}</option>--}}
{{--                                    @foreach ($categories as $category)--}}
{{--                                        <option value="{{ $category['id'] }}"--}}
{{--                                            {{ old('name') == $category['id'] ? 'selected' : '' }}>--}}
{{--                                            {{ $category['defaultName'] }}--}}
{{--                                        </option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3">--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="name" class="title-color">{{ translate('sub_Category') }}</label>--}}
{{--                                <select class="js-select2-custom form-control action-get-request-onchange" name="sub_category_id"--}}
{{--                                        id="sub-category-select"--}}
{{--                                        data-url-prefix="{{ url('/admin/products/get-categories?parent_id=') }}"--}}
{{--                                        data-element-id="sub-sub-category-select"--}}
{{--                                        data-element-type="select">--}}
{{--                                    <option value="{{ null }}" selected--}}
{{--                                            disabled>{{ translate('select_Sub_Category') }}</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3">--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="name" class="title-color">{{ translate('sub_Sub_Category') }}</label>--}}
{{--                                <select class="js-select2-custom form-control" name="sub_sub_category_id"--}}
{{--                                        id="sub-sub-category-select">--}}
{{--                                    <option value="{{ null }}" selected disabled>--}}
{{--                                        {{ translate('select_Sub_Sub_Category') }}--}}
{{--                                    </option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        @if($brandSetting)--}}
{{--                            <div class="col-md-6 col-lg-4 col-xl-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="title-color">{{ translate('brand') }}</label>--}}
{{--                                    <select class="js-select2-custom form-control" name="brand_id" required>--}}
{{--                                        <option value="{{ null }}" selected--}}
{{--                                                disabled>{{ translate('select_Brand') }}</option>--}}
{{--                                        @foreach ($brands as $brand)--}}
{{--                                            <option value="{{ $brand['id'] }}">{{ $brand['defaultName'] }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        <div class="col-md-6 col-lg-4 col-xl-3">--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="title-color">{{ translate('product_type') }}</label>--}}
{{--                                <select name="product_type" id="product_type" class="form-control" required>--}}
{{--                                    <option value="physical" selected>{{ translate('physical') }}</option>--}}
{{--                                    @if($digitalProductSetting)--}}
{{--                                        <option value="digital">{{ translate('digital') }}</option>--}}
{{--                                    @endif--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3" id="digital_product_type_show">--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="digital_product_type"--}}
{{--                                       class="title-color">{{ translate("delivery_type") }}</label>--}}
{{--                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                      title="{{ translate('for_“Ready_Product”_deliveries,_customers_can_pay_&_instantly_download_pre-uploaded_digital_products._For_“Ready_After_Sale”_deliveries,_customers_pay_first,_then_admin_uploads_the_digital_products_that_become_available_to_customers_for_download') }}">--}}
{{--                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                </span>--}}
{{--                                <select name="digital_product_type" id="digital_product_type" class="form-control"--}}
{{--                                        required>--}}
{{--                                    <option value="{{ old('category_id') }}" selected disabled>--}}
{{--                                        ---{{ translate('select') }}-----}}
{{--                                    </option>--}}
{{--                                    <option value="ready_after_sell">{{ translate("ready_After_Sell") }}</option>--}}
{{--                                    <option value="ready_product">{{ translate("ready_Product") }}</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3" id="digital_file_ready_show">--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="d-flex align-items-center gap-2 mb-2">--}}
{{--                                    <label for="digital_file_ready" class="title-color mb-0">--}}
{{--                                        {{ translate("upload_file") }}--}}
{{--                                    </label>--}}

{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          title="{{ translate('upload_the_digital_products_from_here') }}">--}}
{{--                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                                <div class="input-group">--}}
{{--                                    <div class="custom-file">--}}
{{--                                        <input type="file" class="custom-file-input" name="digital_file_ready"--}}
{{--                                               id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">--}}
{{--                                        <label class="custom-file-label"--}}
{{--                                               for="inputGroupFile01">{{ translate('choose_file') }}</label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                                <div class="mt-2">{{ translate('file_type') }}: {{ "jpg, jpeg, png, gif, zip, pdf" }}</div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="col-md-6 col-lg-4 col-xl-3">--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="title-color d-flex justify-content-between gap-2">--}}
{{--                                    <span class="d-flex align-items-center gap-2">--}}
{{--                                        {{ translate('product_SKU') }}--}}
{{--                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                              title="{{ translate('create_a_unique_product_code_by_clicking_on_the_Generate_Code_button') }}">--}}
{{--                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"--}}
{{--                                                 alt="">--}}
{{--                                        </span>--}}
{{--                                    </span>--}}
{{--                                    <span class="style-one-pro cursor-pointer user-select-none text--primary action-onclick-generate-number" data-input="#generate_number">--}}
{{--                                        {{ translate('generate_code') }}--}}
{{--                                    </span>--}}
{{--                                </label>--}}
{{--                                <input type="text" minlength="6" id="generate_number" name="code"--}}
{{--                                       class="form-control" value="{{ old('code') }}"--}}
{{--                                       placeholder="{{ translate('ex').': 161183'}}" required>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3 physical_product_show">--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="title-color">{{ translate('unit') }}</label>--}}
{{--                                <select class="js-example-basic-multiple form-control" name="unit">--}}
{{--                                    @foreach (units() as $unit)--}}
{{--                                        <option value="{{ $unit }}" {{ old('unit') == $unit ? 'selected' : '' }}>--}}
{{--                                            {{ $unit }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-9">--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="title-color d-flex align-items-center gap-2">--}}
{{--                                    {{ translate('search_tags') }}--}}
{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          title="{{ translate('add_the_product_search_tag_for_this_product_that_customers_can_use_to_search_quickly') }}">--}}
{{--                                        <img width="16" src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"--}}
{{--                                             alt="">--}}
{{--                                    </span>--}}
{{--                                </label>--}}
{{--                                <input type="text" class="form-control" placeholder="{{ translate('enter_tag') }}"--}}
{{--                                       name="tags" data-role="tagsinput">--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="card mt-3 rest-part">--}}
{{--                <div class="card-header">--}}
{{--                    <div class="d-flex gap-2">--}}
{{--                        <i class="tio-user-big"></i>--}}
{{--                        <h4 class="mb-0">{{ translate('pricing_&_others') }}</h4>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="card-body">--}}
{{--                    <div class="row align-items-end">--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3 d-none">--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="d-flex gap-2 mb-2">--}}
{{--                                    <label class="title-color mb-0">{{ translate('purchase_price') }}--}}
{{--                                        ({{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}--}}
{{--                                        )</label>--}}

{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          title="{{ translate('add_the_purchase_price_for_this_product') }}.">--}}
{{--                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                                <input type="number" min="0" step="0.01"--}}
{{--                                       placeholder="{{ translate('purchase_price') }}"--}}
{{--                                       value="{{ old('purchase_price') }}" name="purchase_price"--}}
{{--                                       class="form-control" required>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3">--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="d-flex gap-2 mb-2">--}}
{{--                                    <label class="title-color mb-0">{{ translate('unit_price') }}--}}
{{--                                        ({{ getCurrencySymbol(currencyCode: getCurrencyCode()) }})</label>--}}

{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          title="{{ translate('set_the_selling_price_for_each_unit_of_this_product._This_Unit_Price_section_won’t_be_applied_if_you_set_a_variation_wise_price') }}.">--}}
{{--                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                                <input type="number" min="0" step="0.01"--}}
{{--                                       placeholder="{{ translate('unit_price') }}" name="unit_price"--}}
{{--                                       value="{{ old('unit_price') }}" class="form-control" required>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3" id="minimum_order_qty">--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="d-flex gap-2 mb-2">--}}
{{--                                    <label class="title-color mb-0"--}}
{{--                                           for="minimum_order_qty">{{ translate('minimum_order_qty') }}</label>--}}

{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          title="{{ translate('set_the_minimum_order_quantity_that_customers_must_choose._Otherwise,_the_checkout_process_won’t_start') }}.">--}}
{{--                                        <img src="{{ asset('public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </div>--}}

{{--                                <input type="number" min="1" value="1" step="1"--}}
{{--                                       placeholder="{{ translate('minimum_order_quantity') }}" name="minimum_order_qty"--}}
{{--                                       id="minimum_order_qty" class="form-control" required>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3 physical_product_show" id="quantity">--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="d-flex gap-2 mb-2">--}}
{{--                                    <label class="title-color mb-0"--}}
{{--                                           for="current_stock">{{ translate('current_stock_qty') }}</label>--}}

{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          title="{{ translate('add_the_Stock_Quantity_of_this_product_that_will_be_visible_to_customers') }}.">--}}
{{--                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </div>--}}

{{--                                <input type="number" min="0" value="0" step="1"--}}
{{--                                       placeholder="{{ translate('quantity') }}" name="current_stock" id="current_stock"--}}
{{--                                       class="form-control" required>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3">--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="d-flex gap-2 mb-2">--}}
{{--                                    <label class="title-color mb-0"--}}
{{--                                           for="discount_Type">{{ translate('discount_Type') }}</label>--}}

{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          title="{{ translate('if_Flat,_discount_amount_will_be_set_as_fixed_amount._If_Percentage,_discount_amount_will_be_set_as_percentage.') }}">--}}
{{--                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </div>--}}

{{--                                <select class="form-control" name="discount_type" id="discount_type">--}}
{{--                                    <option value="flat">{{ translate('flat') }}</option>--}}
{{--                                    <option value="percent">{{ translate('percent') }}</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3">--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="d-flex gap-2">--}}
{{--                                    <label class="title-color" for="discount">{{ translate('discount_amount') }} <span--}}
{{--                                            class="discount_amount_symbol">({{getCurrencySymbol(currencyCode: getCurrencyCode()) }})</span></label>--}}

{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          title="{{ translate('add_the_discount_amount_in_percentage_or_a_fixed_value_here') }}.">--}}
{{--                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                                <input type="number" min="0" value="0" step="0.01"--}}
{{--                                       placeholder="{{ translate('ex: 5') }}"--}}
{{--                                       name="discount" id="discount" class="form-control" required>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3">--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="d-flex gap-2">--}}
{{--                                    <label class="title-color" for="tax">{{ translate('tax_amount') }}(%)</label>--}}

{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          title="{{ translate('set_the_Tax_Amount_in_percentage_here') }}">--}}
{{--                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </div>--}}

{{--                                <input type="number" min="0" step="0.01"--}}
{{--                                       placeholder="{{ translate('ex: 5') }}" name="tax" id="tax"--}}
{{--                                       value="{{ old('tax') ?? 0 }}" class="form-control">--}}
{{--                                <input name="tax_type" value="percent" class="d-none">--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3">--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="d-flex gap-2">--}}
{{--                                    <label class="title-color"--}}
{{--                                           for="tax_model">{{ translate('tax_calculation') }}</label>--}}

{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          title="{{ translate('set_the_tax_calculation_method_from_here._Select_“Include_with_product”_to_combine_product_price_and_tax_on_the_checkout._Pick_“Exclude_from_product”_to_display_product_price_and_tax_amount_separately.') }}">--}}
{{--                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                                <select name="tax_model" id="tax_model" class="form-control" required>--}}
{{--                                    <option value="include">{{ translate("include_with_product") }}</option>--}}
{{--                                    <option value="exclude">{{ translate("exclude_with_product") }}</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-lg-4 col-xl-3 physical_product_show" id="shipping_cost">--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="d-flex gap-2">--}}
{{--                                    <label class="title-color">{{ translate('shipping_cost') }}--}}
{{--                                        ({{getCurrencySymbol(currencyCode: getCurrencyCode()) }})</label>--}}

{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          title="{{ translate('set_the_shipping_cost_for_this_product_here._Shipping_cost_will_only_be_applicable_if_product-wise_shipping_is_enabled.') }}">--}}
{{--                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </div>--}}

{{--                                <input type="number" min="0" value="0" step="1"--}}
{{--                                       placeholder="{{ translate('shipping_cost') }}" name="shipping_cost"--}}
{{--                                       class="form-control" required>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="col-md-6 physical_product_show" id="shipping_cost_multy">--}}
{{--                            <div class="form-group">--}}
{{--                                <div--}}
{{--                                    class="form-control h-auto min-form-control-height d-flex align-items-center flex-wrap justify-content-between gap-2">--}}
{{--                                    <div class="d-flex gap-2">--}}
{{--                                        <label class="title-color text-capitalize"--}}
{{--                                               for="shipping_cost">{{ translate('shipping_cost_multiply_with_quantity') }}</label>--}}

{{--                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                              title="{{ translate('if_enabled,_the_shipping_charge_will_increase_with_the_product_quantity') }}">--}}
{{--                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"--}}
{{--                                                 alt="">--}}
{{--                                        </span>--}}
{{--                                    </div>--}}

{{--                                    <div>--}}
{{--                                        <label class="switcher">--}}
{{--                                            <input type="checkbox" class="switcher_input" name="multiply_qty">--}}
{{--                                            <span class="switcher_control"></span>--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="card mt-3 rest-part physical_product_show">--}}
{{--                <div class="card-header">--}}
{{--                    <div class="d-flex gap-2">--}}
{{--                        <i class="tio-user-big"></i>--}}
{{--                        <h4 class="mb-0">{{ translate('product_variation_setup') }}</h4>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="card-body">--}}
{{--                    <div class="row align-items-end">--}}
{{--                        <div class="col-md-6">--}}
{{--                            <div class="mb-3 d-flex align-items-center gap-2">--}}
{{--                                <label for="colors" class="title-color mb-0">--}}
{{--                                    {{ translate('select_colors') }} :--}}
{{--                                </label>--}}
{{--                                <label class="switcher">--}}
{{--                                    <input type="checkbox" class="switcher_input" id="product-color-switcher"--}}
{{--                                           value="{{ old('colors_active') }}"--}}
{{--                                           name="colors_active">--}}
{{--                                    <span class="switcher_control"></span>--}}
{{--                                </label>--}}
{{--                            </div>--}}
{{--                            <select--}}
{{--                                class="js-example-basic-multiple js-states js-example-responsive form-control color-var-select"--}}
{{--                                name="colors[]" multiple="multiple" id="colors-selector" disabled>--}}
{{--                                @foreach ($colors as $key => $color)--}}
{{--                                    <option value="{{ $color->code }}">--}}
{{--                                        {{ $color['name'] }}--}}
{{--                                    </option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}

{{--                        <div class="col-md-6">--}}
{{--                            <label for="choice_attributes" class="title-color">--}}
{{--                                {{ translate('select_attributes') }} :--}}
{{--                            </label>--}}
{{--                            <select class="js-example-basic-multiple js-states js-example-responsive form-control"--}}
{{--                                    name="choice_attributes[]" id="choice_attributes" multiple="multiple">--}}
{{--                                @foreach ($attributes as $key => $a)--}}
{{--                                    <option value="{{ $a['id'] }}">--}}
{{--                                        {{ $a['name'] }}--}}
{{--                                    </option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}

{{--                        <div class="col-md-12 mt-2 mb-2">--}}
{{--                            <div class="row customer_choice_options mt-2" id="customer_choice_options"></div>--}}
{{--                            <div class="form-group sku_combination" id="sku_combination"></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="mt-3 rest-part">--}}
{{--                <div class="row g-2">--}}
{{--                    <div class="col-md-3">--}}
{{--                        <div class="card h-100">--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="form-group">--}}
{{--                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">--}}
{{--                                        <div>--}}
{{--                                            <label for="name"--}}
{{--                                                   class="title-color text-capitalize font-weight-bold mb-0">{{ translate('product_thumbnail') }}</label>--}}
{{--                                            <span--}}
{{--                                                class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>--}}
{{--                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                                  title="{{ translate('add_your_product’s_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">--}}
{{--                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"--}}
{{--                                                     alt="">--}}
{{--                                            </span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                    <div>--}}
{{--                                        <div class="custom_upload_input">--}}
{{--                                            <input type="file" name="image" class="custom-upload-input-file action-upload-color-image" id=""--}}
{{--                                                   data-imgpreview="pre_img_viewer"--}}
{{--                                                   accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">--}}

{{--                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">--}}
{{--                                                <i class="tio-delete"></i>--}}
{{--                                            </span>--}}

{{--                                            <div class="img_area_with_preview position-absolute z-index-2">--}}
{{--                                                <img id="pre_img_viewer" class="h-auto aspect-1 bg-white d-none"--}}
{{--                                                     src="dummy" alt="">--}}
{{--                                            </div>--}}
{{--                                            <div--}}
{{--                                                class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">--}}
{{--                                                <div--}}
{{--                                                    class="d-flex flex-column justify-content-center align-items-center">--}}
{{--                                                    <img alt="" class="w-75"--}}
{{--                                                         src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">--}}
{{--                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}

{{--                                        <p class="text-muted mt-2">--}}
{{--                                            {{ translate('image_format') }} : {{ "Jpg, png, jpeg, webp," }}--}}
{{--                                            <br>--}}
{{--                                            {{ translate('image_size') }} : {{ translate('max') }} {{ "2 MB" }}</p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="color_image_column col-md-9 d-none">--}}
{{--                        <div class="card h-100">--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="form-group">--}}
{{--                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">--}}
{{--                                        <div>--}}
{{--                                            <label for="name"--}}
{{--                                                   class="title-color text-capitalize font-weight-bold mb-0">{{ translate('colour_wise_product_image') }}</label>--}}
{{--                                            <span--}}
{{--                                                class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>--}}
{{--                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                                  title="{{ translate('add_color-wise_product_images_here') }}.">--}}
{{--                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"--}}
{{--                                                     alt="">--}}
{{--                                            </span>--}}
{{--                                        </div>--}}

{{--                                    </div>--}}
{{--                                    <p class="text-muted">{{ translate('must_upload_colour_wise_images_first._Colour_is_shown_in_the_image_section_top_right') }}--}}
{{--                                        . </p>--}}

{{--                                    <div id="color-wise-image-section" class="row g-2"></div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="additional_image_column col-md-9">--}}
{{--                        <div class="card h-100">--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">--}}
{{--                                    <div>--}}
{{--                                        <label for="name"--}}
{{--                                               class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_additional_image') }}</label>--}}
{{--                                        <span--}}
{{--                                            class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>--}}
{{--                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                              title="{{ translate('upload_any_additional_images_for_this_product_from_here') }}.">--}}
{{--                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                        </span>--}}
{{--                                    </div>--}}

{{--                                </div>--}}
{{--                                <p class="text-muted">{{ translate('upload_additional_product_images') }}</p>--}}

{{--                                <div class="row g-2" id="additional_Image_Section">--}}
{{--                                    <div class="col-sm-12 col-md-4">--}}
{{--                                        <div class="custom_upload_input position-relative border-dashed-2">--}}
{{--                                            <input type="file" name="images[]" class="custom-upload-input-file action-add-more-image"--}}
{{--                                                   data-index="1" data-imgpreview="additional_Image_1"--}}
{{--                                                   accept=".jpg, .png, .webp, .jpeg, .gif, .bmp, .tif, .tiff|image/*"--}}
{{--                                                   data-target-section="#additional_Image_Section"--}}
{{--                                            >--}}

{{--                                            <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">--}}
{{--                                                <i class="tio-delete"></i>--}}
{{--                                            </span>--}}

{{--                                            <div class="img_area_with_preview position-absolute z-index-2 border-0">--}}
{{--                                                <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none "--}}
{{--                                                     src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}" alt="">--}}
{{--                                            </div>--}}
{{--                                            <div--}}
{{--                                                class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">--}}
{{--                                                <div--}}
{{--                                                    class="d-flex flex-column justify-content-center align-items-center">--}}
{{--                                                    <img alt=""--}}
{{--                                                         src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"--}}
{{--                                                         class="w-75">--}}
{{--                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="card mt-3 rest-part">--}}
{{--                <div class="card-header">--}}
{{--                    <div class="d-flex gap-2">--}}
{{--                        <i class="tio-user-big"></i>--}}
{{--                        <h4 class="mb-0">{{ translate('product_video') }}</h4>--}}
{{--                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                              title="{{ translate('add_the_YouTube_video_link_here._Only_the_YouTube-embedded_link_is_supported') }}.">--}}
{{--                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                        </span>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="card-body">--}}
{{--                    <div class="mb-3">--}}
{{--                        <label class="title-color mb-0">{{ translate('youtube_video_link') }}</label>--}}
{{--                        <span class="text-info"> ({{ translate('optional_please_provide_embed_link_not_direct_link') }}.)</span>--}}
{{--                    </div>--}}
{{--                    <input type="text" name="video_url"--}}
{{--                           placeholder="{{ translate('ex').': https://www.youtube.com/embed/5R06LRdUCSE' }}"--}}
{{--                           class="form-control" required>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="card mt-3 rest-part">--}}
{{--                <div class="card-header">--}}
{{--                    <div class="d-flex gap-2">--}}
{{--                        <i class="tio-user-big"></i>--}}
{{--                        <h4 class="mb-0">--}}
{{--                            {{ translate('seo_section') }}--}}
{{--                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                  data-placement="top"--}}
{{--                                  title="{{ translate('add_meta_titles_descriptions_and_images_for_products').', '.translate('this_will_help_more_people_to_find_them_on_search_engines_and_see_the_right_details_while_sharing_on_other_social_platforms') }}">--}}
{{--                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                            </span>--}}
{{--                        </h4>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="card-body">--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-md-8">--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="title-color">--}}
{{--                                    {{ translate('meta_Title') }}--}}
{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          data-placement="top"--}}
{{--                                          title="{{ translate('add_the_products_title_name_taglines_etc_here').' '.translate('this_title_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') .' [ '. translate('character_Limit') }} : 100 ]">--}}
{{--                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </label>--}}
{{--                                <input type="text" name="meta_title" placeholder="{{ translate('meta_Title') }}"--}}
{{--                                       class="form-control">--}}
{{--                            </div>--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="title-color">--}}
{{--                                    {{ translate('meta_Description') }}--}}
{{--                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                          data-placement="top"--}}
{{--                                          title="{{ translate('write_a_short_description_of_the_InHouse_shops_product').' '.translate('this_description_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') .' [ '. translate('character_Limit') }} : 100 ]">--}}
{{--                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">--}}
{{--                                    </span>--}}
{{--                                </label>--}}
{{--                                <textarea rows="4" type="text" name="meta_description" class="form-control"></textarea>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="col-md-4">--}}
{{--                            <div class="d-flex justify-content-center">--}}
{{--                                <div class="form-group w-100">--}}
{{--                                    <div class="d-flex align-items-center justify-content-between gap-2">--}}
{{--                                        <div>--}}
{{--                                            <label class="title-color" for="meta_Image">--}}
{{--                                                {{ translate('meta_Image') }}--}}
{{--                                            </label>--}}
{{--                                            <span--}}
{{--                                                class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Meta Thumbnail'] }}</span>--}}
{{--                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"--}}
{{--                                                  title="{{ translate('add_Meta_Image_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB, {{ translate('which_will_be_shown_in_search_engine_results') }}.">--}}
{{--                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"--}}
{{--                                                     alt="">--}}
{{--                                            </span>--}}
{{--                                        </div>--}}

{{--                                    </div>--}}

{{--                                    <div>--}}
{{--                                        <div class="custom_upload_input">--}}
{{--                                            <input type="file" name="meta_image"--}}
{{--                                                   class="custom-upload-input-file meta-img action-upload-color-image" id=""--}}
{{--                                                   data-imgpreview="pre_meta_image_viewer"--}}
{{--                                                   accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">--}}

{{--                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">--}}
{{--                                                <i class="tio-delete"></i>--}}
{{--                                            </span>--}}

{{--                                            <div class="img_area_with_preview position-absolute z-index-2">--}}
{{--                                                <img id="pre_meta_image_viewer" class="h-auto bg-white onerror-add-class-d-none" alt=""--}}
{{--                                                     src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}">--}}
{{--                                            </div>--}}
{{--                                            <div--}}
{{--                                                class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">--}}
{{--                                                <div--}}
{{--                                                    class="d-flex flex-column justify-content-center align-items-center">--}}
{{--                                                    <img alt="" class="w-75"--}}
{{--                                                         src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">--}}
{{--                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="row justify-content-end gap-3 mt-3 mx-1">--}}
{{--                <button type="reset" class="btn btn-secondary px-5">{{ translate('reset') }}</button>--}}
{{--                <button type="button" class="btn btn--primary px-5 product-add-requirements-check">{{ translate('submit') }}</button>--}}
{{--            </div>--}}
        </form>

        <div class="modal " id="addnewType" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{translate('Add')}} {{translate('Unit')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form class="col-12" id="unitForm">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{translate('unit')}}</label>
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

    <span id="route-admin-products-sku-combination" data-url="{{ route('admin.products.sku-combination') }}"></span>
    <span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
    <span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
    <span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
    <span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
    <span id="message-file-size-too-big" data-text="{{ translate('file_size_too_big') }}"></span>
    <span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
    <span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
    <span id="message-no-word" data-text="{{ translate('no') }}"></span>
    <span id="message-want-to-add-or-update-this-product" data-text="{{ translate('want_to_add_this_product') }}"></span>
    <span id="message-please-only-input-png-or-jpg" data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
    <span id="message-product-added-successfully" data-text="{{ translate('product_added_successfully') }}"></span>
    <span id="message-discount-will-not-larger-then-variant-price" data-text="{{ translate('the_discount_price_will_not_larger_then_Variant_Price') }}"></span>
    <span id="system-currency-code" data-value="{{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}"></span>
    <span id="system-session-direction" data-value="{{ Session::get('direction') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-colors-img.js') }}"></script>

    <script>
        var index_list = 0;
        $(document).ready(function(){
            $('.select-unit').selectpicker();
        })
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
                                    <label class="control-label">{{translate('Sort')}}</label>
                                    <input type="number" min="1" step="1"
                                            name="prdoctPrice[`+index_list+`][order]" class="form-control"
                                            required>
                                </div>
                                <div class="col-md-6">
                                    <label for="name">{{translate('Unit')}}</label>
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
            <label class="control-label">{{translate('Unit price')}}</label>
                                    <input type="number" min="0" step="0.01"
                                            placeholder="{{translate('Unit price')}}"
                                            name="prdoctPrice[`+index_list+`][unit_price]" value="{{old('unit_price')}}" class="form-control"
                                            required>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-4" id="quantity">
                                    <label
                                        class="control-label">{{translate('numberOfPieces')}}</label>
                                    <input type="number" min="1" value="1" step="1"
                                            placeholder="{{translate('numberOfPieces')}}"
                                            name="prdoctPrice[`+index_list+`][numberOfPieces]" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label
                                        class="control-label">{{translate('Purchase price')}}</label>
                                    <input type="number" min="0" step="0.01"
                                            placeholder="{{translate('Purchase price')}}"
                                            value="{{old('purchase_price')}}"
                                            name="prdoctPrice[`+index_list+`][purchase_price]" class="form-control" required>
                                </div>

                                <div class="col-md-5">
                                    <label class="control-label">{{translate('Tax')}}</label>
                                    <label class="badge badge-info">{{translate('Percent')}} ( % )</label>
                                    <input type="number" min="0" value="0" step="0.01"
                                            placeholder="{{translate('Tax')}}}" name="prdoctPrice[`+index_list+`][tax]"
                                            value="{{old('tax')}}"
                                            class="form-control">
                                    <input name="prdoctPrice[`+index_list+`][tax_type]" value="percent" style="display: none">
                                </div>

                                <div class="col-md-5">
                                    <label class="control-label">{{translate('Discount')}}</label>
                                    <input type="number" min="0" value="{{old('discount')}}" step="0.01"
                                            placeholder="{{translate('Discount')}}" name="prdoctPrice[`+index_list+`][discount]"
                                            class="form-control" required>
                                </div>
                                <div class="col-md-2" style="padding-top: 30px;">
                                    <select style="width: 100%"
                                        class="js-example-basic-multiple js-states js-example-responsive demo-select2"
                                        name="prdoctPrice[`+index_list+`][discount_type]">
                                        <option value="flat">{{translate('Flat')}}</option>
                                        <option value="percent">{{translate('Percent')}}</option>
                                    </select>
                                </div>
                                {{-- <div class="pt-4 col-12 sku_combination" id="sku_combination">

                                </div> --}}
                <div class="col-sm-12 col-md-12 col-lg-12" id="shipping_cost">
                    <label
                        class="control-label">{{translate('description')}} </label>
                                    <textarea placeholder="{{translate('description')}}"
                                           name="prdoctPrice[`+index_list+`][description]" class="form-control" required></textarea>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-4" id="shipping_cost">
                                    <label
                                        class="control-label">{{translate('shipping_cost')}} </label>
                                    <input type="number" min="0" value="0" step="1"
                                            placeholder="{{translate('shipping_cost')}}"
                                            name="prdoctPrice[`+index_list+`][shipping_cost]" class="form-control" required>
                                </div>
                                <div class="col-md-6 col-lg-4 mt-sm-1" id="shipping_cost_multy">
                                    <div>
                                        <label
                                        class="control-label">{{translate('shipping_cost_multiply_with_quantity')}} </label>

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
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: 10,
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
                    toastr.error('{{translate('Please only input png or jpg type file')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{translate('File size too big')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

            $("#thumbnail").spartanMultiImagePicker({
                fieldName: 'image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-12',
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
                    toastr.error('{{translate('Please only input png or jpg type file')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{translate('File size too big')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

            $("#meta_img").spartanMultiImagePicker({
                fieldName: 'meta_image',
                maxCount: 1,
                rowHeight: '280px',
                groupClassName: 'col-12',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('public/assets/back-end/img/400x400/img2.jpg')}}',
                    width: '90%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{translate('Please only input png or jpg type file')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{translate('File size too big')}}', {
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
            // dir: "rtl",
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
            $('#customer_choice_options').append('<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="{{trans('Choice Title') }}" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="{{trans('Enter choice values') }}" data-role="tagsinput" onchange="update_sku()"></div></div>');

            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }


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
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }

        $(document).ready(function () {
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
        function check(){
            Swal.fire({
                title: '{{translate('Are you sure')}}?',
                text: '',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#377dff',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
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
                    url: '{{route('admin.product.store')}}',
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
                            toastr.success('{{translate('product added successfully')}}!', {
                                CloseButton: true,
                                ProgressBar: true
                            });
                            $('#product_form').submit();
                        }
                    }
                });
            })
        };
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

@endpush
