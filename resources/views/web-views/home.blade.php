@extends('layouts.front-end.app')

@section('title',translate('Welcome To').' '.$web_config['name']->value)

@push('css_or_js')
    <meta property="og:image" content="{{asset('storage/app/public/company')}}/{{$web_config['web_logo']->value}}"/>
    <meta property="og:title" content="Welcome To {{$web_config['name']->value}} Home"/>
    <meta property="og:url" content="{{env('APP_URL')}}">
    <meta property="og:description" content="{!! substr($web_config['about']->value,0,100) !!}">

    <meta property="twitter:card" content="{{asset('storage/app/public/company')}}/{{$web_config['web_logo']->value}}"/>
    <meta property="twitter:title" content="Welcome To {{$web_config['name']->value}} Home"/>
    <meta property="twitter:url" content="{{env('APP_URL')}}">
    <meta property="twitter:description" content="{!! substr($web_config['about']->value,0,100) !!}">

    <link rel="stylesheet" href="{{asset('public/assets/front-end')}}/css/home.css"/>
    <style>
        .media {
            background: white;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
        }

        .cz-countdown-days {
            color: white !important;
            background-color: #ffffff30;
            border: .5px solid{{$web_config['primary_color']}};
            padding: 0px 6px;
            border-radius: 3px;
            margin-right: 0px !important;
            display: flex;
	        flex-direction: column;
            -ms-flex: .4;  /* IE 10 */
            flex: 1;

        }

        .cz-countdown-hours {
            color: white !important;
            background-color: #ffffff30;
            border: .5px solid{{$web_config['primary_color']}};
            padding: 0px 6px;
            border-radius: 3px;
            margin-right: 0px !important;
            display: flex;
	        flex-direction: column;
            -ms-flex: .4;  /* IE 10 */
            flex: 1;
        }

        .cz-countdown-minutes {
            color: white !important;
            background-color: #ffffff30;
            border: .5px solid{{$web_config['primary_color']}};
            padding: 0px 6px;
            border-radius: 3px;
            margin-right: 0px !important;
            display: flex;
	        flex-direction: column;
            -ms-flex: .4;  /* IE 10 */
            flex: 1;
        }

        .cz-countdown-seconds {
            color: white !important;
            background-color: #ffffff30;
            border: .5px solid{{$web_config['primary_color']}};
            padding: 0px 6px;
            border-radius: 3px;
            display: flex;
	        flex-direction: column;
            -ms-flex: .4;  /* IE 10 */
            flex: 1;
        }

        .flash_deal_product_details .flash-product-price {
            font-weight: 700;
            font-size: 18px;
            color: {{$web_config['primary_color']}};
        }

        .featured_deal_left {
            height: 130px;
            background: {{$web_config['primary_color']}} 0% 0% no-repeat padding-box;
            padding: 10px 13px;
            text-align: center;
        }

        .category_div:hover {
            color: {{$web_config['secondary_color']}};
        }

        .deal_of_the_day {
            /* filter: grayscale(0.5); */
            /* opacity: .8; */
            background: {{$web_config['secondary_color']}};
            border-radius: 3px;
        }

        .deal-title {
            font-size: 12px;

        }

        .for-flash-deal-img img {
            max-width: none;
        }
        .best-selleing-image {
            background:{{$web_config['primary_color']}}10;
            width:30%;
            display:flex;
            align-items:center;
            border-radius: 5px;
        }
        .best-selling-details {
            padding:10px;
            width:50%;
        }
        .top-rated-image{
            background:{{$web_config['primary_color']}}10;
            width:30%;
            display:flex;
            align-items:center;
            border-radius: 5px;
        }
        .top-rated-details {
            padding:10px;width:70%;
        }

        @media (max-width: 375px) {
            .cz-countdown {
                display: flex !important;

            }

            .cz-countdown .cz-countdown-seconds {

                margin-top: -5px !important;
            }

            .for-feature-title {
                font-size: 20px !important;
            }
        }

        @media (max-width: 600px) {
            .flash_deal_title {
                /*font-weight: 600;*/
                /*font-size: 18px;*/
                /*text-transform: uppercase;*/

                font-weight: 700;
                font-size: 25px;
                text-transform: uppercase;
            }

            .cz-countdown .cz-countdown-value {
                /* font-family: "Roboto", sans-serif; */
                font-size: 11px !important;
                font-weight: 700 !important;

            }

            .featured_deal {
                opacity: 1 !important;
            }

            .cz-countdown {
                display: inline-block;
                flex-wrap: wrap;
                font-weight: normal;
                margin-top: 4px;
                font-size: smaller;
            }

            .view-btn-div-f {

                margin-top: 6px;
                float: right;
            }

            .view-btn-div {
                float: right;
            }

            .viw-btn-a {
                font-size: 10px;
                font-weight: 600;
            }


            .for-mobile {
                display: none;
            }

            .featured_for_mobile {
                max-width: 100%;
                margin-top: 20px;
                margin-bottom: 20px;
            }
            .best-selleing-image {
                width: 50%;
                border-radius: 5px;
            }
            .best-selling-details {
                width:50%;
            }
            .top-rated-image {
                width: 50%;
            }
            .top-rated-details {
            width:50%;
        }
        }


        @media (max-width: 360px) {
            .featured_for_mobile {
                max-width: 100%;
                margin-top: 10px;
                margin-bottom: 10px;
            }

            .featured_deal {
                opacity: 1 !important;
            }
        }

        @media (max-width: 375px) {
            .featured_for_mobile {
                max-width: 100%;
                margin-top: 10px;
                margin-bottom: 10px;
            }

            .featured_deal {
                opacity: 1 !important;
            }

        }

        @media (min-width: 768px) {
            .displayTab {
                display: block !important;
            }

        }

        @media (max-width: 800px) {

            .latest-product-margin {
                margin-left: 0px !important;
                }
            .for-tab-view-img {
                width: 40%;
            }

            .for-tab-view-img {
                width: 105px;
            }

            .widget-title {
                font-size: 19px !important;
            }
            .flash-deal-view-all-web {
                display: none !important;
            }
            .categories-view-all {
                {{session('direction') === "rtl" ? 'margin-left: 10px;' : 'margin-right: 6px;'}}
            }
            .categories-title {
                {{Session::get('direction') === "rtl" ? 'margin-right: 0px;' : 'margin-left: 6px;'}}
            }
            .seller-list-title{
                {{Session::get('direction') === "rtl" ? 'margin-right: 0px;' : 'margin-left: 10px;'}}
            }
            .seller-list-view-all {
                {{Session::get('direction') === "rtl" ? 'margin-left: 20px;' : 'margin-right: 10px;'}}
            }
            .seller-card {
                padding-left: 0px !important;
            }
            .category-product-view-title {
                {{Session::get('direction') === "rtl" ? 'margin-right: 16px;' : 'margin-left: -8px;'}}
            }
            .category-product-view-all {
                {{Session::get('direction') === "rtl" ? 'margin-left: -7px;' : 'margin-right: 5px;'}}
            }
            .recomanded-product-card {
                background: #F8FBFD;margin:20px;height: 535px; border-radius: 5px;
            }
            .recomanded-buy-button {
                text-align: center;
                margin-top: 30px;
            }
        }
        @media(min-width:801px){
            .flash-deal-view-all-mobile{
                display: none !important;
            }
            .categories-view-all {
                {{session('direction') === "rtl" ? 'margin-left: 30px;' : 'margin-right: 27px;'}}
            }
            .categories-title {
                {{Session::get('direction') === "rtl" ? 'margin-right: 25px;' : 'margin-left: 25px;'}}
            }
            .seller-list-title{
                {{Session::get('direction') === "rtl" ? 'margin-right: 6px;' : 'margin-left: 10px;'}}
            }
            .seller-list-view-all {
                {{Session::get('direction') === "rtl" ? 'margin-left: 12px;' : 'margin-right: 10px;'}}
            }
            .seller-card {
                {{Session::get('direction') === "rtl" ? 'padding-left:0px !important;' : 'padding-right:0px !important;'}}
            }
            .category-product-view-title {
                {{Session::get('direction') === "rtl" ? 'margin-right: 10px;' : 'margin-left: -12px;'}}
            }
            .category-product-view-all {
                {{Session::get('direction') === "rtl" ? 'margin-left: -20px;' : 'margin-right: 0px;'}}
            }
            .recomanded-product-card {
                background: #F8FBFD;margin:20px;height: 475px; border-radius: 5px;
            }
            .recomanded-buy-button {
                text-align: center;
                margin-top: 63px;
            }

        }

        .featured_deal_carosel .carousel-inner {
            width: 100% !important;
        }

        .badge-style2 {
            color: black !important;
            background: transparent !important;
            font-size: 11px;
        }
        .countdown-card{
            background:{{$web_config['primary_color']}}10;
            height: 150px!important;
            border-radius:5px;

        }
        .flash-deal-text{
            color: {{$web_config['primary_color']}};
            text-transform: uppercase;
            text-align:center;
            font-weight:700;
            font-size:20px;
            border-radius:5px;
            margin-top:25px;
        }
        .countdown-background{
            background: {{$web_config['primary_color']}};
            padding: 5px 5px;
            border-radius:5px;
            margin-top:15px;
        }
        .carousel-wrap{
            position: relative;
        }
        .owl-nav{
            top: 40%;
            position: absolute;
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
     }
     .owl-prev{
         float: left;

     }
     .owl-next{
         float: right;
     }
     .czi-arrow-left{
        color: {{$web_config['primary_color']}};
        background: {{$web_config['primary_color']}}10;
        padding: 5px;
        border-radius: 50%;
        margin-left: -12px;
        font-weight: bold;
        font-size: 12px;
     }
     .czi-arrow-right{
        color: {{$web_config['primary_color']}};
        background: {{$web_config['primary_color']}}10;
        padding: 5px;
        border-radius: 50%;
        margin-right: -15px;
        font-weight: bold;
        font-size: 12px;
     }
    .owl-carousel .nav-btn .czi-arrow-left{
      height: 47px;
      position: absolute;
      width: 26px;
      cursor: pointer;
      top: 100px !important;
  }
  .flash-deals-background-image{
    background: {{$web_config['primary_color']}}10;
    border-radius:5px;
    width:125px;
    height:125px;
  }
  .view-all-text{
    color:{{$web_config['secondary_color']}} !important;
    font-size:14px;
  }
  .feature-product-title {
    text-align: center;
    font-size: 22px;
    margin-top: 15px;
    font-style: normal;
    font-weight: 700;
  }
  .feature-product .czi-arrow-left{
        color: {{$web_config['primary_color']}};
        background: {{$web_config['primary_color']}}10;
        padding: 5px;
        border-radius: 50%;
        margin-left: -80px;
        font-weight: bold;
        font-size: 12px;
     }

     .feature-product .owl-nav{
        top: 40%;
        position: absolute;
        display: flex;
        justify-content: space-between;
        /* width: 100%; */
        z-index: -999;
    }
     .feature-product .czi-arrow-right{
        color: {{$web_config['primary_color']}};
        background: {{$web_config['primary_color']}}10;
        padding: 5px;
        border-radius: 50%;
        margin-right: -80px;
        font-weight: bold;
        font-size: 12px;
     }
     .shipping-policy-web{
        background: #ffffff;width:100%; border-radius:5px;
     }
     .shipping-method-system{
        height: 130px;width: 70%;margin-top: 15px;
     }

     .flex-between {
         display: flex;
         justify-content: space-between;
     }
     .new_arrival_product .czi-arrow-left{
         margin-left: -28px;
     }
     .new_arrival_product .owl-nav{
         z-index: -999;
     }
     .rating-show , .flash-product-review {
        display: none !important;
        opacity: 0;
    }
    .bonus-product:hover > .quick-view , .flash_deal_product:hover > .quick-view {
        display: block;
    }

    .bonus-product {
        min-height: 210px;
    }

    /**/
    .icons-open{
        position: fixed;
        width: 200px;
        background: #f00;
        left: 1%;
        bottom: 25%;
    }
    .icons {
        position: fixed;
        width: 200px;
        background: #f00;
        left: 1%;
        bottom: 12%;
        z-index:9999;
    }

    .bonus-icon {
        display:none;
    }
    .icons-open .bonus-icon{
        display:flex;
    }

    .bonus-icon-open {

    }

    .bonus-icon {
        color:#fff;
        font-size:22px;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        text-align: center;
        /* align-content: center; */
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        background-color:#040458;
    }

    .sub-icon.sub-icon-1.bonus-icon {}

    .sub-icon.sub-icon-1.bonus-icon {
        top: -60px;
    }
    .bonus-icon-facebook{

        background-color:#1877F2;
    }

    .sub-icon.sub-icon-2.bonus-icon {
        left: 60px;
        top: -25px;
    }
    .bonus-icon-WhatsApp{

        background-color:#25d366;
    }

    .sub-icon.sub-icon-3.bonus-icon {
        left: 60px;
        top: 40px;
    }
    .bonus-icon-phone{

        background-color:red;
    }

    .sub-icon.sub-icon-4.bonus-icon {
        top: 60px;
    }
    .bonus-icon-telegram{

        background-color:#0088cc;
    }
    .main-icon.bonus-icon {
        background: #1458a7;
    }

    .bonus-icon {
        display: none;
    }

    \ {
    }

    .main-icon.bonus-icon {
        display: flex;
    }
    .icons-open .main-icon.bonus-icon {
        display: display;
    }
    .icons-open .main-icon-close.bonus-icon {
        display: flex;
    }
    /**/
    </style>

    <link rel="stylesheet" href="{{asset('public/assets/front-end')}}/css/owl.carousel.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/front-end')}}/css/owl.theme.default.min.css"/>
@endpush

@section('content')

@php($decimal_point_settings = \App\CPU\Helpers::get_business_settings('decimal_point_settings'))
    <!-- Hero (Banners + Slider)-->
    <section class="bg-transparent mb-3">
        <div class="container">
            <div class="row ">
                <div class="col-12">
                    @include('web-views.partials._home-top-slider')
                </div>
            </div>
        </div>
    </section>

    {{--flash deal--}}
    @php($flash_deals=\App\Model\FlashDeal::with(['products'=>function($query){
        $query->with('product')->whereHas('product',function($q){
            $q->where('status',1);
        });
}])->where(['status'=>1])->where(['deal_type'=>'flash_deal'])->whereDate('start_date','<=',date('Y-m-d'))->whereDate('end_date','>=',date('Y-m-d'))->first())

    @if (isset($flash_deals))
    <div class="container">
        <div class="flash-deal-view-all-web row d-flex justify-content-{{Session::get('direction') === "rtl" ? 'start' : 'end'}}" style="{{Session::get('direction') === "rtl" ? 'margin-left: 2px;' : 'margin-right:2px;'}}">
            <a class="text-capitalize view-all-text" href="{{route('flash-deals',[isset($flash_deals)?$flash_deals['id']:0])}}">
                {{ translate('view_all')}}
                <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
            </a>
        </div>
        <div class="row d-flex {{Session::get('direction') === "rtl" ? 'flex-row-reverse' : 'flex-row'}}">


            <div class="col-md-3 mt-2 countdown-card" >
                <div class="m-2">
                    <div class="flash-deal-text">
                        <span>{{ translate('flash deal')}}</span>
                    </div>
                    <div style=" text-align: center;color: #ffffff !important;">
                        <div class="countdown-background">
                            <span class="cz-countdown d-flex justify-content-center align-items-center"
                                data-countdown="{{isset($flash_deals)?date('m/d/Y',strtotime($flash_deals['end_date'])):''}} 11:59:00 PM">
                                <span class="cz-countdown-days">
                                    <span class="cz-countdown-value"></span>
                                    <span>{{ translate('day')}}</span>
                                </span>
                                <span class="cz-countdown-value p-1">:</span>
                                <span class="cz-countdown-hours">
                                    <span class="cz-countdown-value"></span>
                                    <span>{{ translate('hrs')}}</span>
                                </span>
                                <span class="cz-countdown-value p-1">:</span>
                                <span class="cz-countdown-minutes">
                                    <span class="cz-countdown-value"></span>
                                    <span>{{ translate('min')}}</span>
                                </span>
                                <span class="cz-countdown-value p-1">:</span>
                                <span class="cz-countdown-seconds">
                                    <span class="cz-countdown-value"></span>
                                    <span>{{ translate('sec')}}</span>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flash-deal-view-all-mobile col-md-12" style="{{Session::get('direction') === "rtl" ? 'margin-left: 2px;' : 'margin-right:2px;'}}">
                <a class="{{Session::get('direction') === "rtl" ? 'float-left' : 'float-right'}} mt-2 text-capitalize view-all-text" href="{{route('flash-deals',[isset($flash_deals)?$flash_deals['id']:0])}}">
                    {{ translate('view_all')}}
                    <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
                </a>
            </div>
            <div class="col-md-9 {{Session::get('direction') === "rtl" ? 'pr-md-4' : 'pl-md-4'}}">
                <div class="carousel-wrap">
                    <div class="owl-carousel owl-theme mt-2" id="flash-deal-slider">
                        @foreach($flash_deals->products as $key=>$deal)
                            @if( $deal->product)
                                @include('web-views.partials._product-card-1',['product'=>$deal->product,'decimal_point_settings'=>$decimal_point_settings])
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @php($business_mode=\App\CPU\Helpers::get_business_settings('business_mode'))
    {{--categries--}}
    <div class="container rtl">
        <div class="row">
            @if ($business_mode == 'multi')
                <div class="col-md-12 {{Session::get('direction') === "rtl" ? 'pr-0' : 'pl-0'}}">
                    <div class="card" style="min-height: 200px;">
                        <div class="card-body">
                            <div class="row d-flex justify-content-between">
                                <div class="categories-title">
                                    <span style="font-weight: 600;font-size: 16px;">{{ translate('categories')}}</span>
                                </div>
                                <div class="categories-view-all">
                                    <a class="text-capitalize view-all-text"
                                    href="{{route('categories')}}">{{ translate('view_all')}}
                                    <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center mt-3">
                                @foreach($categories as $key=>$category)

                                    @if ($key<10)
                                    <div class="text-center"  style="margin: 5px;">
                                        <a href="{{route('products',['id'=> $category['id'],'data_from'=>'category','page'=>1])}}">
                                            <img style="vertical-align: middle; height: 100px;border-radius: 5px;"
                                                onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                src="{{asset("storage/app/public/category/$category->icon")}}"
                                                alt="{{$category->name}}">
                                            <p class="text-center small "
                                            style="margin-top: 5px">{{Str::limit($category->name, 12)}}</p>
                                        </a>
                                    </div>
                                    @endif

                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-md-12 pl-0 pr-0">
                    <div class="card" style="min-height: 232px;">
                        <div class="card-body">
                            <div class="row d-flex justify-content-between">
                                <div style="{{Session::get('direction') === "rtl" ? 'margin-right: 20px;' : 'margin-left: 22px;'}}">
                                    <span style="font-weight: 600;font-size: 16px;">{{ translate('categories')}}</span>
                                </div>
                                <div style="{{Session::get('direction') === "rtl" ? 'margin-left: 15px;' : 'margin-right: 13px;'}}">
                                    <a class="text-capitalize view-all-text"
                                    href="{{route('categories')}}">{{ translate('view_all')}}
                                    <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center mt-3">
                                @foreach($categories as $key=>$category)
                                    @if ($key<11)
                                        <div class="text-center"  style="margin: 5px;">
                                            <!--<a href="{{route('products',['id'=> $category['id'],'data_from'=>'category','page'=>1])}}">-->
                                            <a href="{{url('categories/'.$category['id'])}}">
                                                <img style="vertical-align: middle; height: 100px;border-radius: 5px;"
                                                    onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                    src="{{asset("storage/app/public/category/$category->icon")}}"
                                                    alt="{{$category->name}}">
                                                <p class="text-center small "
                                                style="margin-top: 5px">{{Str::limit($category->name, 12)}}</p>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <!-- top sellers -->

        @if ($business_mode == 'multi')
            @if(count($top_sellers) > 0)
                <div class="col-md-12 mt-2 mt-md-0 seller-card" >
                    <div class="card" style="min-height: 183px;">
                        <div class="card-body">
                            <div class="row d-flex justify-content-between">
                                <div class="seller-list-title">
                                    <span style="font-weight: 600;font-size: 18px;">{{ translate('sellers')}}</span>
                                </div>
                                <div class="seller-list-view-all">
                                    <a class="text-capitalize view-all-text"
                                    href="{{route('sellers')}}">{{ translate('view_all')}}
                                    <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-between mt-3">
                                @foreach($top_sellers as $key=>$seller)
                                    @if ($key<10)

                                        @if($seller->shop)
                                            <div style="margin: 5px;">
                                                <center>
                                                    <a href="{{route('shopView',['id'=>$seller['id']])}}">
                                                        <img
                                                            style="vertical-align: middle; padding: 2%;width:100px;height: 100px;border-radius: 50%"
                                                            onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                            src="{{asset("storage/app/public/shop")}}/{{$seller->shop->image}}">
                                                        <p class="text-center small ">{{Str::limit($seller->shop->name, 14)}}</p>
                                                    </a>
                                                </center>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
        </div>
    </div>
    {{--brands--}}
    <section class="container rtl mt-3">
        <!-- Heading-->
        <div class="section-header">
            <div style="color: black;font-weight: 700;
            font-size: 22px;">
                <span> {{translate('brands')}}</span>
            </div>
            <div style="margin-right:2px;">
                <a class="text-capitalize view-all-text" href="{{route('brands')}}">
                    {{ translate('view_all')}}
                    <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
                </a>
            </div>
        </div>
    {{--<hr class="view_border">--}}
    <!-- Grid-->

        <div class="mt-3 mb-3 brand-slider">
            <div class="owl-carousel owl-theme p-2" id="brands-slider">
                @foreach($brands as $brand)
                    <div class="text-center">
                        <a href="{{route('products',['id'=> $brand['id'],'data_from'=>'brand','page'=>1])}}">
                            <div class="d-flex align-items-center justify-content-center"
                                 style="height:100px;margin:5px;">
                                <img style="border-radius: 50%;"
                                    onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                    src="{{asset("storage/app/public/brand/$brand->image")}}" alt="{{$brand->name}}">
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Products grid (featured products)-->
    @if ($featured_products->count() > 0 )
    <div class="container mb-4">
        <div class="row" style="background: white;box-shadow: 0px 3px 6px #0000000d;border-radius: 5px;">
            <div class="col-md-12" >
                <div class="feature-product-title">
                    {{ translate('featured_products')}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="feature-product" style="padding-left:55px;padding-right: 55px;padding-top: 10px;">
                    <div class="carousel-wrap p-1">
                        <div class="owl-carousel owl-theme " id="featured_products_list">
                            @foreach($featured_products as $product)
                                <div  style="margin:5px;margin-bottom: 30px;">
                                    @include('web-views.partials._feature-product',['product'=>$product, 'decimal_point_settings'=>$decimal_point_settings])

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{--featured deal--}}
    @php($featured_deals=\App\Model\FlashDeal::with(['products'=>function($query_one){
        $query_one->with('product.reviews')->whereHas('product',function($query_two){
            $query_two->where('status',1);
        });
    }])
    ->where(['status'=>1])->where(['deal_type'=>'feature_deal'])
    ->first())

    @if(isset($featured_deals))
        <section class="container featured_deal rtl mb-2">
            <div class="row" style="background: {{$web_config['primary_color']}};padding:5px;padding-bottom: 25px; border-radius:5px;">
                <div class="col-12 pb-2" >
                    <a class="text-capitalize mt-2 mt-md-0 {{Session::get('direction') === "rtl" ? 'float-left' : 'float-right'}}" href="{{route('products',['data_from'=>'featured_deal'])}}"
                        style="color: white !important;{{Session::get('direction') === "rtl" ? 'margin-left: 21px;' : 'margin-right: 21px;'}}">
                        {{ translate('view_all')}}
                        <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-4 d-flex align-items-center justify-content-center right">
                    <div class="m-4">
                        <span class="featured_deal_title"
                            style="padding-top: 12px">{{ translate('featured_deal')}}</span>
                        <br>

                        <span style="color: white;text-align: left !important;">{{ translate('See the latest deals and exciting new offers ')}}!</span>

                    </div>

                </div>

                <div class="col-xl-9 col-md-8 d-flex align-items-center justify-content-center {{Session::get('direction') === "rtl" ? 'pl-4' : 'pr-4'}}">
                    <div class="owl-carousel owl-theme" id="web-feature-deal-slider">
                        @foreach($featured_deals->products as $key=>$product)
                            @include('web-views.partials._feature-deal-product',['product'=>$product->product, 'decimal_point_settings'=>$decimal_point_settings])
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
    {{--deal of the day--}}
    <div class="container rtl">
        <div class="row">

            {{-- Latest products --}}
            <?php
            /*
            <div class="col-xl-12 col-md-8 mt-2 pl-0 pr-0">
                <div class="latest-product-margin" style="margin-{{Session::get('direction') === "rtl" ? 'right:30px' : 'left:30px'}}">
                    <div class="d-flex justify-content-between">
                        <div class="" style="text-align: center;">
                            <span class="for-feature-title" style="text-align: center;font-size:22px !important; font-weight:700">{{ translate('latest_products')}}</span>
                        </div>
                        <div style="margin-right: 4px;">
                            <a class="text-capitalize view-all-text"
                               href="{{route('products',['data_from'=>'latest'])}}">
                                {{ translate('view_all')}}
                                <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
                            </a>
                        </div>
                    </div>

                    <div class="row mt-2">
                        @foreach($latest_products as $product)
                            <div class="col-xl-3 col-sm-6 col-md-6 col-6 mb-4">
                                <div style="margin:2px;">
                                    @include('web-views.partials._single-product',['product'=>$product,'decimal_point_settings'=>$decimal_point_settings])
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            */
            ?>
        </div>
    </div>


@php($main_section_banner = \App\Model\Banner::where('banner_type','Main Section Banner')->where('published',1)->orderBy('id','desc')->latest()->first())
    @if (isset($main_section_banner))
    <div class="container rtl mb-3">
        <div class="row" >
            <div class="col-12 pl-0 pr-0">
                <a href="{{$main_section_banner->url}}"
                    style="cursor: pointer;">
                    <img class="d-block footer_banner_img" style="width: 100%;border-radius: 5px;height: auto !important;"
                            onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                            src="{{asset('storage/app/public/banner')}}/{{$main_section_banner['photo']}}">
                </a>
            </div>
        </div>
    </div>
    @endif



    <div class="container rtl mt-3">
            <div class="row d-flex justify-content-between align-items-baseline">
            <div class="d-flex">
                <div style="height: 90px;width:90px;">
                    <img  src="{{asset("public/assets/front-end/png/new-arrivals.png")}}"
                                     alt="">

                </div>
                <div style="margin-top:24px;font-weight: 700;font-size: 26px;">
                    <p style="float: right">{{ translate('ARRIVALS')}}</p>
                </div>
            </div>


            <div style="margin-right: 4px;">
                <a class="text-capitalize view-all-text"
                   href="{{route('products',['data_from'=>'latest'])}}">
                    {{ translate('view_all')}}
                    <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
                </a>
            </div>
        </div>


    </div>
    <div class="container rtl mb-3" style="">
        <div class="col-md-12" style="background-color:white;padding:20px;border-radius:10px;">
            <div class="new_arrival_product" style="margin-left:-5px;">
                <div class="carousel-wrap" >
                    <div class="owl-carousel owl-theme p-2" id="new-arrivals-product">
                        @foreach($latest_products as $key=>$product)

                                @include('web-views.partials._product-card-1',['product'=>$product,'decimal_point_settings'=>$decimal_point_settings])

                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container rtl">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-between m-3">
                            <div>
                                <img style="height:30px;width:30px;"  src="{{asset("public/assets/front-end/png/best sellings.png")}}"
                                         alt="">
                                    <span style="margin-left:10px;text-transform: uppercase;font-weight: 700;">{{ translate('best sellings')}}</span>
                            </div>
                            <div>
                                <a class="text-capitalize view-all-text"
                                    href="{{route('products',['data_from'=>'best-selling','page'=>1])}}">{{ translate('view_all')}}
                                    <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
                                </a>
                            </div>
                        </div>
                        <div class="row ml-2 mr-3 mb-2">
                            @foreach($bestSellProduct as $key=>$bestSell)
                                @if($bestSell->product && $key<3)


                                    <?php
                                        $variation = json_decode($bestSell->product->variation);
                                        $price = 0;
                                        $discount = 0;
                                        $discount_type = 'flat';
                                        $discount_precient = 0;
                                        if(isset($variation[0]))
                                        {

                                            $price = $variation[0]->price;
                                            if(isset($variation[0]->discount))
                                            {
                                                $discount = $variation[0]->discount;
                                                $discount_type = $variation[0]->discount_type;
                                                if($variation[0]->discount_type == "percent")
                                                {

                                                    $discount_precient = $variation[0]->discount;
                                                    $discount = round((($variation[0]->discount * $variation[0]->price) / 100) ,2);
                                                }
                                            }


                                        }
                                    ?>


                                    <div class="col-12 m-1 bonus-product" style="border-style: solid;
                                    border-color: #0000000d; border-radius:5px;"
                                         data-href="{{route('product',$bestSell->product->slug)}}">
                                         @if($discount > 0)
                                                <div class="d-flex" style="top:0;position:absolute;{{Session::get('direction') === "rtl" ? 'right:0;' : 'left:0;'}}">
                                                    <span class="for-discoutn-value p-1 pl-2 pr-2" style="{{Session::get('direction') === "rtl" ? 'border-radius:0px 5px' : 'border-radius:5px 0px'}};">
                                                        @if ($bestSell->product->discount_type == 'percent')
                                                            {{round($discount_precient,$decimal_point_settings)}}%
                                                        @elseif($bestSell->product->discount_type =='flat')
                                                            {{\App\CPU\Helpers::currency_converter($discount)}}
                                                        @endif {{translate('off')}}
                                                    </span>
                                                </div>
                                            @endif
                                        <div class="row" style="padding:8px;">

                                            <div class="best-selleing-image"  >
                                                <a class="d-block d-flex justify-content-center" style="width:100%;height:100%;"
                                                    href="{{route('product',$bestSell->product->slug)}}">
                                                    <img style="border-radius:5px;"
                                                        onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                        src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$bestSell->product['thumbnail']}}"
                                                        alt="Product"/>
                                                </a>
                                            </div>
                                            <div class="best-selling-details" style="">
                                                <h6 class="widget-product-title">
                                                    <a class="ptr"
                                                    href="{{route('product',$bestSell->product->slug)}}">
                                                        {{\Illuminate\Support\Str::limit($bestSell->product['name'],100)}}
                                                    </a>
                                                </h6>
                                                @php($bestSell_overallRating = \App\CPU\ProductManager::get_overall_rating($bestSell->product['reviews']))
                                                <div class="rating-show">
                                                    <span class="d-inline-block font-size-sm text-body">
                                                        @for($inc=0;$inc<5;$inc++)
                                                            @if($inc<$bestSell_overallRating[0])
                                                                <i class="sr-star czi-star-filled active"></i>
                                                            @else
                                                                <i class="sr-star czi-star" style="color:#fea569 !important"></i>
                                                            @endif
                                                        @endfor
                                                        <label class="badge-style">( {{$bestSell->product->reviews_count}} )</label>
                                                    </span>
                                                </div>
                                                <div>
                                                    @if($discount > 0)
                                                            <strike style="font-size: 12px!important;color: #E96A6A!important;">
                                                                {{\App\CPU\Helpers::currency_converter($price)}}
                                                            </strike>
                                                    @endif
                                                </div>
                                                <div class="widget-product-meta">
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
                                                   onclick="quickView('{{$bestSell->product->id}}')">
                                                    <i class="czi-eye align-middle {{Session::get('direction') === "rtl" ? 'ml-1' : 'mr-1'}}"></i>
                                                    {{translate('Quick')}}   {{translate('View')}}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-2 mt-md-0">
                <div class="card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-between m-3">
                            <div>
                                <img style="height:30px;width:30px;"  src="{{asset("public/assets/front-end/png/top-rated.png")}}"
                                         alt="">
                                    <span style="margin-left:10px;text-transform: uppercase;font-weight: 700;">{{ translate('top rated')}}</span>
                            </div>
                            <div>
                                <a class="text-capitalize view-all-text"
                                    href="{{route('products',['data_from'=>'top-rated','page'=>1])}}">{{ translate('view_all')}}
                                    <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
                                </a>
                            </div>
                        </div>
                        <div class="row ml-2 mr-3 mb-2">
                            @foreach($topRated as $key=>$top)
                                @if($top->product && $key<3)


                                    <?php
                                        $variation = json_decode($top->product->variation);
                                        $price = 0;
                                        $discount = 0;
                                        $discount_type = 'flat';
                                        $discount_precient = 0;
                                        if(isset($variation[0]))
                                        {

                                            $price = $variation[0]->price;
                                            if(isset($variation[0]->discount))
                                            {
                                                $discount = $variation[0]->discount;
                                                $discount_type = $variation[0]->discount_type;
                                                if($variation[0]->discount_type == "percent")
                                                {

                                                    $discount_precient = $variation[0]->discount;
                                                    $discount = round((($variation[0]->discount * $variation[0]->price) / 100) ,2);
                                                }
                                            }


                                        }
                                    ?>
                                    <div class="col-12 m-1 bonus-product" style="border-style: solid;
                                    border-color: #0000000d; border-radius:5px;"
                                         data-href="{{route('product',$top->product->slug)}}">
                                         @if($discount > 0)
                                                <div class="d-flex" style="top:0;position:absolute;{{Session::get('direction') === "rtl" ? 'right:0;' : 'left:0;'}}">
                                                    <span class="for-discoutn-value p-1 pl-2 pr-2" style="{{Session::get('direction') === "rtl" ? 'border-radius:0px 5px' : 'border-radius:5px 0px'}};">
                                                        @if ($top->product->discount_type == 'percent')
                                                            {{round($discount_precient,$decimal_point_settings)}}%
                                                        @elseif($top->product->discount_type =='flat')
                                                            {{\App\CPU\Helpers::currency_converter($discount)}}
                                                        @endif {{translate('off')}}
                                                    </span>
                                                </div>
                                            @endif
                                        <div class="row" style="padding:8px;">

                                            <div class="top-rated-image">
                                                <a class="d-block d-flex justify-content-center" style="width:100%;height:100%;"
                                                    href="{{route('product',$top->product->slug)}}">
                                                    <img style="border-radius:5px;"
                                                        onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                        src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$top->product['thumbnail']}}"
                                                        alt="Product"/>
                                                </a>
                                            </div>
                                            <div class="top-rated-details" >
                                                <h6 class="widget-product-title">
                                                    <a class="ptr"
                                                    href="{{route('product',$top->product->slug)}}">
                                                        {{\Illuminate\Support\Str::limit($top->product['name'],100)}}
                                                    </a>
                                                </h6>
                                                @php($top_overallRating = \App\CPU\ProductManager::get_overall_rating($top->product['reviews']))
                                                <div class="rating-show">
                                                    <span class="d-inline-block font-size-sm text-body">
                                                        @for($inc=0;$inc<5;$inc++)
                                                            @if($inc<$top_overallRating[0])
                                                                <i class="sr-star czi-star-filled active"></i>
                                                            @else
                                                                <i class="sr-star czi-star" style="color:#fea569 !important"></i>
                                                            @endif
                                                        @endfor
                                                        <label class="badge-style">( {{$top->product->reviews_count}} )</label>
                                                    </span>
                                                </div>
                                                <div>
                                                    @if($discount > 0)
                                                            <strike style="font-size: 12px!important;color: #E96A6A!important;">
                                                                {{\App\CPU\Helpers::currency_converter($price)}}
                                                            </strike>
                                                    @endif
                                                </div>
                                                <div class="widget-product-meta">
                                                    <span class="text-accent">
                                                        {{\App\CPU\Helpers::currency_converter(
                                                        $price - $discount
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
                                                   onclick="quickView('{{$top->product->id}}')">
                                                    <i class="czi-eye align-middle {{Session::get('direction') === "rtl" ? 'ml-1' : 'mr-1'}}"></i>
                                                    {{translate('Quick')}}   {{translate('View')}}
                                                </a>
                                            @endif
                                        </div>

                                    </div>
                                @endif
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Banner  --}}
    <div class="container rtl mt-3 mb-3">
        <div class="row">
            @foreach(\App\Model\Banner::where('banner_type','Footer Banner')->where('published',1)->orderBy('id','desc')->take(2)->get() as $banner)
                <div class="col-md-6 mt-2 mt-md-0">
                    <a href="{{$banner->url}}"
                        style="cursor: pointer;">
                         <img class="" style="width: 100%; border-radius:5px;height:auto;"
                              onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                              src="{{asset('storage/app/public/banner')}}/{{$banner['photo']}}">
                     </a>
                </div>
            @endforeach
        </div>
    </div>
    {{-- Categorized product --}}
    @foreach($home_categories as $category)
        <section class="container rtl mb-3">
            <!-- Heading-->
            <div style="background: #ffffff; padding:20px;border-radius:5px;">
                <div class="flex-between pl-4">
                    <div class="category-product-view-title" >
                        <span class="for-feature-title {{Session::get('direction') === "rtl" ? 'float-right' : 'float-left'}}"
                                style="font-weight: 700;font-size: 20px;text-transform: uppercase;{{Session::get('direction') === "rtl" ? 'text-align:right;' : 'text-align:left;'}}">
                                {{Str::limit($category['name'],18)}}
                        </span>
                    </div>
                    <div class="category-product-view-all" >
                        <a class="text-capitalize view-all-text "
                            href="{{route('products',['id'=> $category['id'],'data_from'=>'category','page'=>1])}}">{{ translate('view_all')}}
                            <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left-circle mr-1 ml-n1 mt-1 float-left' : 'right-circle ml-1 mr-n1'}}"></i>
                        </a>

                    </div>
                </div>

                <div class="row mt-2 mb-3 d-flex justify-content-between">
                    <div class="col-md-3 col-12 pl-3 pr-3">
                        <a href="{{route('products',['id'=> $category['id'],'data_from'=>'category','page'=>1])}}"
                            style="cursor: pointer;">
                             <img class="" style="width: 100%; border-radius:5px;height: 300px;"
                                  onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                  src="{{asset('storage/app/public/category')}}/{{$category['icon']}}">
                         </a>
                    </div>
                     <div class="col-md-9 col-12 ">
                        <div class="row d-flex" >
                            @foreach($category['products'] as $key=>$product)
                            @if ($key<4)
                                <div class="col-md-3 col-6 mt-2 mt-md-0" style="">
                                    @include('web-views.partials._category-single-product',['product'=>$product,'decimal_point_settings'=>$decimal_point_settings])
                                </div>
                            @endif
                        @endforeach
                         </div>
                    </div>


                </div>
            </div>
        </section>
    @endforeach

        {{--delivery type --}}

    <div class="container rtl mb-3">
        <div class="row shipping-policy-web" style="margin-right: 0px; margin-left:0px;">
            <div class="col-md-3 d-flex justify-content-center">
                <div class="shipping-method-system" >
                    <div style="text-align: center;">
                        <img style="height: 60px;width:60px;" src="{{asset("public/assets/front-end/png/delivery.png")}}"
                                 alt="">
                    </div>
                    <div style="text-align: center;">
                        <p>
                        {{ translate('Fast Delivery all accross the country')}}
                    </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-center">
                <div class="shipping-method-system">
                    <div style="text-align: center;">
                        <img style="height: 60px;width:60px;" src="{{asset("public/assets/front-end/png/Payment.png")}}"
                                 alt="">
                    </div>
                    <div style="text-align: center;">
                        <p>
                        {{ translate('Safe Payment')}}
                    </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-center">
                <div class="shipping-method-system">
                    <div style="text-align: center;">
                        <img style="height: 60px;width:60px;" src="{{asset("public/assets/front-end/png/money.png")}}"
                                 alt="">
                    </div>
                    <div style="text-align: center;">
                        <p>
                        {{ translate('7 Days Return Policy')}}
                    </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-center">
                <div class="shipping-method-system">
                    <div style="text-align: center;">
                        <img style="height: 60px;width:60px;" src="{{asset("public/assets/front-end/png/Genuine.png")}}"
                                 alt="">
                    </div>
                    <div style="text-align: center;">
                        <p>
                        {{ translate('100% Authentic Products')}}
                    </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="icons">
        <div class="main-icon bonus-icon">
            <i class="fa fa-solid fa-bars"></i>
        </div>
        <div class="main-icon-close bonus-icon">
            X
        </div>

        <?php
        $socialI = 0;
        ?>
        @if(($socialMedia['bonus_facebook']))
            <?php
            $socialI ++;
            ?>
                <a href="<?=$socialMedia['bonus_facebook']?>">
                    <div class="sub-icon sub-icon-{{$socialI}} bonus-icon-facebook bonus-icon">
                            <i class="fa fa-solid fa-facebook"></i>
                    </div>
                </a>
        @endif

        @if(($socialMedia['bonus_WhatsApp']))
            <?php
            $socialI ++;
            ?>
                <a href="<?=$socialMedia['bonus_WhatsApp']?>">
                    <div class="sub-icon sub-icon-{{$socialI}} bonus-icon-WhatsApp bonus-icon">
                            <i class="fa fa-solid fa-whatsapp"></i>
                    </div>
                </a>
        @endif

        @if(($socialMedia['bonus_phone']))
            <?php
            $socialI ++;
            ?>
                <a href="<?=$socialMedia['bonus_phone']?>">
                    <div class="sub-icon sub-icon-{{$socialI}} bonus-icon-phone bonus-icon">
                            <i class="fa fa-solid fa-phone"></i>
                    </div>
                </a>
        @endif

        @if(($socialMedia['bonus_telegram']))
            <?php
            $socialI ++;
            ?>
                <a href="<?=$socialMedia['bonus_telegram']?>">
                    <div class="sub-icon sub-icon-{{$socialI}} bonus-icon-telegram bonus-icon">
                            <i class="fa fa-solid fa-telegram"></i>
                    </div>
                </a>
        @endif

    </div>
@endsection

@push('script')
    {{-- Owl Carousel --}}
    <script src="{{asset('public/assets/front-end')}}/js/owl.carousel.min.js"></script>

    <script>
        $('#flash-deal-slider').owlCarousel({
            loop: false,
            autoplay: false,
            margin: 5,
            nav: true,
            navText: ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
            dots: false,
            autoplayHoverPause: true,
            '{{session('direction')}}': false,
            // center: true,
            responsive: {
                //X-Small
                0: {
                    items: 1
                },
                360: {
                    items: 1
                },
                375: {
                    items: 1
                },
                540: {
                    items: 2
                },
                //Small
                576: {
                    items: 2
                },
                //Medium
                768: {
                    items: 2
                },
                //Large
                992: {
                    items: 2
                },
                //Extra large
                1200: {
                    items: 2
                },
                //Extra extra large
                1400: {
                    items: 3
                }
            }
        })

        $('#web-feature-deal-slider').owlCarousel({
            loop: false,
            autoplay: true,
            margin: 5,
            nav: false,
            //navText: ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
            dots: false,
            autoplayHoverPause: true,
            '{{session('direction')}}': true,
            // center: true,
            responsive: {
                //X-Small
                0: {
                    items: 1
                },
                360: {
                    items: 1
                },
                375: {
                    items: 1
                },
                540: {
                    items: 2
                },
                //Small
                576: {
                    items: 2
                },
                //Medium
                768: {
                    items: 2
                },
                //Large
                992: {
                    items: 2
                },
                //Extra large
                1200: {
                    items: 2
                },
                //Extra extra large
                1400: {
                    items: 2
                }
            }
        })

        $('#new-arrivals-product').owlCarousel({
            loop: true,
            autoplay: false,
            margin: 5,
            nav: true,
            navText: ["<i class='czi-arrow-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}'></i>", "<i class='czi-arrow-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}'></i>"],
            dots: false,
            autoplayHoverPause: true,
            '{{session('direction')}}': true,
            // center: true,
            responsive: {
                //X-Small
                0: {
                    items: 1
                },
                360: {
                    items: 1
                },
                375: {
                    items: 1
                },
                540: {
                    items: 2
                },
                //Small
                576: {
                    items: 2
                },
                //Medium
                768: {
                    items: 2
                },
                //Large
                992: {
                    items: 2
                },
                //Extra large
                1200: {
                    items: 4
                },
                //Extra extra large
                1400: {
                    items: 4
                }
            }
        })
    </script>
<script>
    $('#featured_products_list').owlCarousel({
        loop: true,
            autoplay: false,
            margin: 5,
            nav: true,
            navText: ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
            dots: false,
            autoplayHoverPause: true,
            '{{session('direction')}}': false,
            // center: true,
            responsive: {
                //X-Small
                0: {
                    items: 1
                },
                360: {
                    items: 1
                },
                375: {
                    items: 1
                },
                540: {
                    items: 2
                },
                //Small
                576: {
                    items: 2
                },
                //Medium
                768: {
                    items: 3
                },
                //Large
                992: {
                    items: 4
                },
                //Extra large
                1200: {
                    items: 5
                },
                //Extra extra large
                1400: {
                    items: 5
                }
            }
        });
</script>
    <script>
        $('#brands-slider').owlCarousel({
            loop: false,
            autoplay: false,
            margin: 10,
            nav: false,
            '{{session('direction')}}': true,
            //navText: ["<i class='czi-arrow-left'></i>","<i class='czi-arrow-right'></i>"],
            dots: true,
            autoplayHoverPause: true,
            // center: true,
            responsive: {
                //X-Small
                0: {
                    items: 2
                },
                360: {
                    items: 3
                },
                375: {
                    items: 3
                },
                540: {
                    items: 4
                },
                //Small
                576: {
                    items: 5
                },
                //Medium
                768: {
                    items: 7
                },
                //Large
                992: {
                    items: 9
                },
                //Extra large
                1200: {
                    items: 11
                },
                //Extra extra large
                1400: {
                    items: 12
                }
            }
        })
    </script>

    <script>
        $('#category-slider, #top-seller-slider').owlCarousel({
            loop: false,
            autoplay: false,
            margin: 5,
            nav: false,
            // navText: ["<i class='czi-arrow-left'></i>","<i class='czi-arrow-right'></i>"],
            dots: true,
            autoplayHoverPause: true,
            '{{session('direction')}}': true,
            // center: true,
            responsive: {
                //X-Small
                0: {
                    items: 2
                },
                360: {
                    items: 3
                },
                375: {
                    items: 3
                },
                540: {
                    items: 4
                },
                //Small
                576: {
                    items: 5
                },
                //Medium
                768: {
                    items: 6
                },
                //Large
                992: {
                    items: 8
                },
                //Extra large
                1200: {
                    items: 10
                },
                //Extra extra large
                1400: {
                    items: 11
                }
            }
        })

        $('.main-icon').on("click",function(e){
           $('.icons').addClass("icons-open");
        });
        $('.main-icon-close').on("click",function(e){
           $('.icons').removeClass("icons-open");
        });
    </script>
@endpush

