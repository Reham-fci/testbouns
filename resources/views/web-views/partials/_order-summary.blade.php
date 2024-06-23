<style>
    .cart_title {
        font-weight: 400 !important;
        font-size: 16px;
    }

    .cart_value {
        font-weight: 600 !important;
        font-size: 16px;
    }

    .cart_total_value {
        font-weight: 700 !important;
        font-size: 25px !important;
        color: {{$web_config['primary_color']}}     !important;
    }
</style>

<aside class="col-lg-4 pt-4 pt-lg-0">
    <div class="cart_total">
        @php($sub_total=0)
        @php($total_tax=0)
        @php($total_shipping_cost=0)
        @php($total_discount_on_product=0)
        @php($cart=\App\CPU\CartManager::get_cart())
        @php($shipping_cost=\App\CPU\CartManager::get_shipping_cost())
        @if($cart->count() > 0)
            @foreach($cart as $key => $cartItem)
                @php($sub_total+=$cartItem['price']*$cartItem['quantity'])
                @php($total_tax+=$cartItem['tax']*$cartItem['quantity'])
                @php($total_discount_on_product+=$cartItem['discount']*$cartItem['quantity'])
            @endforeach
            @php($total_shipping_cost=$shipping_cost)
        @else
            <span>{{translate('empty_cart')}}</span>
        @endif
        @if(isset($wallet))
        <div class="d-flex justify-content-between">
            <span class="cart_title">
                {{translate('bouns_value')}}
                <span class="wallet-value">{{\App\CPU\Helpers::currency_converter($wallet)}}</span>
            </span>

        </div>
        @endif
        <div class="d-flex justify-content-between">
            <span class="cart_title">{{translate('sub_total')}}</span>
            <span class="cart_value">
                {{\App\CPU\Helpers::currency_converter($sub_total)}}
            </span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="cart_title">{{translate('tax')}}</span>
            <span class="cart_value">
                {{\App\CPU\Helpers::currency_converter($total_tax)}}
            </span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="cart_title">{{translate('shipping')}}</span>
            <span class="cart_value">
                {{\App\CPU\Helpers::currency_converter($total_shipping_cost)}}
            </span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="cart_title">{{translate('discount_on_product')}}</span>
            <span class="cart_value">
                - {{\App\CPU\Helpers::currency_converter($total_discount_on_product)}}
            </span>
        </div>

        <div class="d-flex justify-content-between">
            <span class="cart_title">{{translate('wallet')}}</span>
            <span class="cart_value wallet_value">
                {{\App\CPU\Helpers::currency_converter(0)}}
            </span>
        </div>
        @if(isset($max_amount))
        <div class="mt-2">
            <form class="needs-validation <?php if(!isset($ispayment)){echo "d-none";}elseif(!$ispayment){echo "d-none";} ?>" action="javascript:" method="post" novalidate
            id="walletValueForm">
                <div class="form-group">
                    <input class="form-control walletValue" type="number" step="any" min="0" max="{{$max_amount}}" name="walletValue" placeholder="{{translate('use_bonus_value')}}"
                           required>

                </div>
                <button class="btn btn-primary btn-block" type="button" onclick="apply_wallet()">{{translate('apply_wallet')}}
                </button>
            </form>
        </div>
        @endif
        @if(session()->has('coupon_discount'))
            @php($coupon_dis=session('coupon_discount'))
        @else
            @php($coupon_dis=0)
        @endif
        <div class="d-flex justify-content-between">
            <span class="cart_title">{{translate('coupon_discount')}}</span>
            <span class="cart_value" id="coupon-discount-amount">
                - {{session()->has('coupon_discount')?\App\CPU\Helpers::currency_converter(session('coupon_discount')):0}}
            </span>
        </div>
        <div class="mt-2">
            <form class="needs-validation <?php if(!isset($ispayment)){echo "d-none";}elseif(!$ispayment){echo "d-none";} ?>" action="javascript:" method="post" novalidate id="coupon-code-ajax">
                <div class="form-group">
                    <input class="form-control input_code" type="text" name="code" placeholder="{{translate('Coupon code')}}"
                           required>
                    <div class="invalid-feedback">{{translate('please_provide_coupon_code')}}</div>
                </div>
                <button class="btn btn-primary btn-block" type="button" onclick="couponCode()">{{translate('apply_code')}}
                </button>
            </form>
        </div>
        <hr class="mt-2 mb-2">
        <div class="d-flex justify-content-between">
            <span class="cart_title">{{translate('total')}}</span>
            <span class="cart_value">
               {{\App\CPU\Helpers::currency_converter($sub_total+$total_tax+$total_shipping_cost-$coupon_dis-$total_discount_on_product)}}
            </span>
        </div>

        {{-- <div class="d-flex justify-content-center">
            <span class="cart_total_value mt-2">
                {{\App\CPU\Helpers::currency_converter($sub_total+$total_tax+$total_shipping_cost-$coupon_dis-$total_discount_on_product)}}
            </span>
        </div> --}}
    </div>
    <div class="container mt-2">
        <div class="row p-0">
            <div class="col-md-3 p-0 text-center mobile-padding">
                <img class="order-summery-footer-image" src="{{asset("public/assets/front-end/png/delivery.png")}}" alt="">
                <div class="deal-title">3 {{translate('days_free_delivery')}} </div>
            </div>

            <div class="col-md-3 p-0 text-center">
                <img class="order-summery-footer-image" src="{{asset("public/assets/front-end/png/money.png")}}" alt="">
                <div class="deal-title">{{translate('money_back_guarantee')}}</div>
            </div>
            <div class="col-md-3 p-0 text-center">
                <img class="order-summery-footer-image" src="{{asset("public/assets/front-end/png/Genuine.png")}}" alt="">
                <div class="deal-title">100% {{translate('genuine')}} {{translate('product')}}</div>
            </div>
            <div class="col-md-3 p-0 text-center">
                <img class="order-summery-footer-image" src="{{asset("public/assets/front-end/png/Payment.png")}}" alt="">
                <div class="deal-title">{{translate('authentic_payment')}}</div>
            </div>
        </div>
    </div>
</aside>
