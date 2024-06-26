@if(isset($product))

    @php
        $variation = json_decode($product->variation);
        $price = 0;
        $discount = 0;
        if(isset($variation[0])){

            $price = $variation[0]->price;
            if(isset($variation[0]->discount)){
                $discount = $variation[0]->discount;
                if($variation[0]->discount_type == "percent"){

                    $discount = round((($variation[0]->discount * $variation[0]->price) / 100) ,2);
                }
            }


        }
    @endphp
    @php($overallRating = \App\CPU\ProductManager::get_overall_rating($product->reviews))
    <!--<div class="flash_deal_product rtl" style="cursor: pointer; height:150px;{{Session::get('direction') === "rtl" ? 'margin-right:6px;' : 'margin-left:6px;'}}"-->
    <!--     onclick="location.href='{{route('product',$product->slug)}}'">-->
    <div class="flash_deal_product rtl" style="cursor: pointer; height:180px;{{Session::get('direction') === "rtl" ? 'margin-right:6px;' : 'margin-left:6px;'}}">
        @if($product->discount > 0)
        <div class="d-flex" style="top:0;position:absolute;">
            <span class="for-discoutn-value p-1 pl-2 pr-2" style="{{Session::get('direction') === "rtl" ? 'border-radius:0px 5px' : 'border-radius:5px 0px'}};background:#FF5555 !important;">
                @if ($product->discount_type == 'percent')
                    {{round($product->discount,$decimal_point_settings)}}%
                @elseif($product->discount_type =='flat')
                    {{\App\CPU\Helpers::currency_converter($product->discount)}}
                @endif {{translate('off')}}
            </span>
        </div>
        @endif
        <div class=" d-flex" style="">
            <div class="d-flex align-items-center justify-content-center"
                 style="padding-{{Session::get('direction') === "rtl" ?'right:12px':'left:12px'}};padding-top:12px;">
                <div class="flash-deals-background-image">
                    <img style="height: 125px!important;width:125px!important;border-radius:5px;"
                     src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$product['thumbnail']}}"
                     onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"/>
                </div>
            </div>
            <div class="flash_deal_product_details pl-3 pr-3 pr-1 d-flex align-items-center">
                <div>
                    <div>
                        <span class="flash-product-title">
                            {{$product['name']}}
                        </span>
                    </div>
                    <?php /*
                    <div class="flash-product-review">
                        @for($inc=0;$inc<5;$inc++)
                            @if($inc<$overallRating[0])
                                <i class="sr-star czi-star-filled active"></i>
                            @else
                                <i class="sr-star czi-star" style="color:#fea569 !important"></i>
                            @endif
                        @endfor
                        <label class="badge-style2">
                            ( {{$product->reviews->count()}} )
                        </label>
                    </div>
                    */?>
                    <div>
                        @if($discount > 0)
                            <strike
                                style="font-size: 12px!important;color: #E96A6A!important;">
                                {{\App\CPU\Helpers::currency_converter($price)}}
                            </strike>
                        @endif
                    </div>
                    <div class="flash-product-price">
                        {{\App\CPU\Helpers::currency_converter($price-$discount)}}

                    </div>

                </div>
            </div>
        </div>
        <div class="text-center quick-view" >
            @if(Request::is('product/*'))
                <a class="btn btn-primary btn-sm" href="{{route('product',$product->slug)}}">
                    <i class="czi-forward align-middle {{Session::get('direction') === "rtl" ? 'ml-1' : 'mr-1'}}"></i>
                    {{translate('View')}}
                </a>
            @else
                <a class="btn btn-primary btn-sm"
                style="margin-top:0px;padding-top:5px;padding-bottom:5px;padding-left:10px;padding-right:10px;" href="javascript:"
                   onclick="quickView('{{$product->id}}')">
                    <i class="czi-eye align-middle {{Session::get('direction') === "rtl" ? 'ml-1' : 'mr-1'}}"></i>
                    {{translate('Quick')}}   {{translate('View')}}
                </a>
            @endif
        </div>
    </div>
@endif
