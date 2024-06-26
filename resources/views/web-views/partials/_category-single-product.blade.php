@php($overallRating = \App\CPU\ProductManager::get_overall_rating($product->reviews))

<style>
    .quick-view{
        display: none;
        padding-bottom: 8px;
    }
    .product-single-hover{
        box-shadow: 0px 0px 5px rgba(0, 113, 220, 0.15);
        border-radius: 5px;
    }
    .quick-view , .single-product-details{
        background: #ffffff;
    }
    .product-single-hover:hover > .single-product-details {

        margin-top:-39px;
    }
    .product-single-hover:hover >  .quick-view{
        display: block;
    }
</style>

<div class="product-single-hover" >



    <?php
        $variation = json_decode($product->variation);
        $price = 0;
        $discount = 0;
        $discount_type = 'flat';
        $discount_precient = 0;
        if(isset($variation[0])){

            $price = $variation[0]->price;
            if(isset($variation[0]->discount)){
                $discount = $variation[0]->discount;
                $discount_type = $variation[0]->discount_type;
                if($variation[0]->discount_type == "percent"){

                    $discount_precient = $variation[0]->discount;
                    $discount = round((($variation[0]->discount * $variation[0]->price) / 100) ,2);
                }
            }


        }
    ?>
    @if($product->current_stock <= 0)
        <span style="position: absolute;z-index: 999;background: rgb(255 0 0 / 100%);text-align: center;color: #000;font-weight: bolder;top: 0;left: 0;width: 150px;">{{translate('not_available')}}</span>
    @endif
    <div class=" inline_product clickable d-flex justify-content-center"
            style="cursor: pointer;background:{{$web_config['primary_color']}}10;max-height: 195px;">
        @if($product->discount > 0)
            <div class="d-flex" style="left:5px;top:0px;position: absolute">
                    <span class="for-discoutn-value p-1 pl-2 pr-2">
                    @if ($product->discount_type == 'percent')
                            {{round($product->discount,$decimal_point_settings)}}%
                        @elseif($product->discount_type =='flat')
                            {{\App\CPU\Helpers::currency_converter($product->discount)}}
                        @endif
                        {{translate('off')}}
                    </span>
            </div>
        @else
            <div class="d-flex justify-content-end for-dicount-div-null">
                <span class="for-discoutn-value-null"></span>
            </div>
        @endif
        <div class="d-flex d-block " style="cursor: pointer;">
            <a href="{{route('product',$product->slug)}}">
                <img src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$product['thumbnail']}}"
                    onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                    style="width: 100%;border-radius: 5px 5px 0px 0px;">
            </a>
        </div>
    </div>
    <div class="single-product-details" style="position:relative;min-height:105px;border-radius: 0px 0px 5px 5px; ">
        <div class="text-center">
            <a href="{{route('product',$product->slug)}}" style="font-weight: 400;
                font-size: 13px; ">
                {{ Str::limit($product['name'], 18) }}
            </a>
        </div>
        <!--<div class="rating-show justify-content-between text-center">-->
        <!--    <span class="d-inline-block font-size-sm text-body" style="font-weight: 400;-->
        <!--    font-size: 10px;">-->
        <!--        @for($inc=0;$inc<5;$inc++)-->
        <!--            @if($inc<$overallRating[0])-->
        <!--                <i class="sr-star czi-star-filled active"></i>-->
        <!--            @else-->
        <!--                <i class="sr-star czi-star" style="color:#fea569 !important"></i>-->
        <!--            @endif-->
        <!--        @endfor-->
        <!--        <label class="badge-style">( {{$product->reviews_count}} )</label>-->
        <!--    </span>-->
        <!--</div>-->
        <div class="justify-content-between text-center">
            <div class="product-price text-center" style="font-weight: 400;
            font-size: 12px;">
                @if($discount > 0)
                    <strike style="font-size: 12px!important;color: #E96A6A!important;">
                        {{\App\CPU\Helpers::currency_converter($price)}}
                    </strike><br>
                @endif
                <span class="text-accent">
                    {{\App\CPU\Helpers::currency_converter(
                        $price-$discount
                    )}}
                </span>
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


