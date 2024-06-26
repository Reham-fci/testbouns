@extends('layouts.back-end.app')

@section('title', translate('Order Details'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="{{ asset('public/assets/select2/css/select2.min.css')}}" rel="stylesheet">
    <style>
        .sellerName {
            height: fit-content;
            margin-top: 10px;
            margin-left: 10px;
            font-size: 16px;
            border-radius: 25px;
            text-align: center;
            padding-top: 10px;
        }
        *, ::after, ::before {
    box-sizing: border-box;
}
tr,td,th{
    width: fit-content;
}
input{
    width:80px;
}
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header d-print-none p-3" style="background: white">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="{{route('admin.orders.list',['status'=>'all'])}}">{{translate('Orders')}}</a>
                            </li>
                            <li class="breadcrumb-item active"
                                aria-current="page">{{translate('Order')}} {{translate('details')}} </li>
                        </ol>
                    </nav>

                    <div class="d-sm-flex align-items-sm-center">
                        <h1 class="page-header-title">{{translate('Order')}} #{{$order['id']}}</h1>

                        @if($order['payment_status']=='paid')
                            <span class="badge badge-soft-success ml-sm-3">
                                <span class="legend-indicator bg-success"></span>{{translate('Paid')}}
                            </span>
                        @else
                            <span class="badge badge-soft-danger ml-sm-3">
                                <span class="legend-indicator bg-danger"></span>{{translate('Unpaid')}}
                            </span>
                        @endif

                        @if($order['order_status']=='pending')
                            <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-info text"></span>{{str_replace('_',' ',$order['order_status'])}}
                            </span>
                        @elseif($order['order_status']=='failed')
                            <span class="badge badge-danger ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-info"></span>{{str_replace('_',' ',$order['order_status'])}}
                            </span>
                        @elseif($order['order_status']=='processing' || $order['order_status']=='out_for_delivery')
                            <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-warning"></span>{{str_replace('_',' ',$order['order_status'])}}
                            </span>
                        @elseif($order['order_status']=='delivered' || $order['order_status']=='confirmed')
                            <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-success"></span>{{str_replace('_',' ',$order['order_status'])}}
                            </span>
                        @else
                            <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-danger"></span>{{str_replace('_',' ',$order['order_status'])}}
                            </span>
                        @endif
                        <span class="ml-2 ml-sm-3">
                        <i class="tio-date-range"></i> {{date('d M Y H:i:s',strtotime($order['created_at']))}}
                        </span>

                        @if(\App\CPU\Helpers::get_business_settings('order_verification'))
                            <span class="ml-2 ml-sm-3">
                                <b>
                                    {{translate('order_verification_code')}} : {{$order['verification_code']}}
                                </b>
                            </span>
                        @endif

                    </div>
                    <div class="col-md-6 mt-2">
                        <a class="text-body mr-3" target="_blank"
                           href={{route('admin.orders.generate-invoice',[$order['id']])}}>
                            <i class="tio-print mr-1"></i> {{translate('Print')}} {{translate('invoice')}}
                        </a>

                        @if (isset($shipping_address['latitude']) && isset($shipping_address['longitude']))
                            <button class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#locationModal"><i
                                    class="tio-map"></i> {{translate('show_locations_on_map')}}</button>
                        @else
                            <button class="btn btn-xs btn-warning"><i
                                    class="tio-map"></i> {{translate('shipping_address_has_been_given_below')}}
                            </button>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 mt-4">
                            <label class="badge badge-info">{{translate('linked_orders')}}
                                : {{$linked_orders->count()}}</label><br>
                            @foreach($linked_orders as $linked)
                                <a href="{{route('admin.orders.details',[$linked['id']])}}"
                                   class="btn btn-secondary">{{translate('ID')}}
                                    :{{$linked['id']}}</a>
                            @endforeach
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="hs-unfold float-right col-4">
                                <div class="dropdown">
                                    <select name="order_status" onchange="order_status(this.value)"
                                            class="status form-control"
                                            data-id="{{$order['id']}}">

                                        <option
                                            value="pending" {{$order->order_status == 'pending'?'selected':''}} > {{translate('Pending')}}</option>
                                        <option
                                            value="confirmed" {{$order->order_status == 'confirmed'?'selected':''}} > {{translate('Confirmed')}}</option>
                                        <option
                                            value="processing" {{$order->order_status == 'processing'?'selected':''}} >{{translate('Processing')}} </option>
                                        <option class="text-capitalize"
                                                value="out_for_delivery" {{$order->order_status == 'out_for_delivery'?'selected':''}} >{{translate('out_for_delivery')}} </option>
                                        <option
                                            value="delivered" {{$order->order_status == 'delivered'?'selected':''}} >{{translate('Delivered')}} </option>
                                        <option
                                            value="returned" {{$order->order_status == 'returned'?'selected':''}} > {{translate('Returned')}}</option>
                                        <option
                                            value="failed" {{$order->order_status == 'failed'?'selected':''}} >{{translate('Failed')}} </option>
                                        <option
                                            value="canceled" {{$order->order_status == 'canceled'?'selected':''}} >{{translate('Canceled')}} </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4 hs-unfold float-right pr-2">
                                <div class="dropdown">
                                    <select name="payment_status" class="payment_status form-control"
                                            data-id="{{$order['id']}}">

                                        <option
                                            onclick="route_alert('{{route('admin.orders.payment-status',['id'=>$order['id'],'payment_status'=>'paid'])}}','Change status to paid ?')"
                                            href="javascript:"
                                            value="paid" {{$order->payment_status == 'paid'?'selected':''}} >
                                            {{translate('Paid')}}
                                        </option>
                                        <option value="unpaid" {{$order->payment_status == 'unpaid'?'selected':''}} >
                                            {{translate('Unpaid')}}
                                        </option>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Unfold -->
                </div>
            </div>
        </div>

        <!-- End Page Header -->

        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header" style="display: block!important;">
                        <div class="row">
                            <div class="col-12 pb-2 border-bottom">
                                <h4 class="card-header-title">
                                    {{translate('Order')}} {{translate('details')}}
                                    <span
                                        class="badge badge-soft-dark rounded-circle ml-1">{{$order->details->count()}}</span>
                                </h4>
                            </div>

                            <div class="col-3 pt-2">
                                @if ($order->order_note !=null)
                                    <span class="font-weight-bold text-capitalize">
                                        {{translate('order_note')}} :
                                    </span>
                                    <p class="pl-1">
                                        {{$order->order_note}}
                                    </p>
                                @endif
                            </div>
                            <div class="col-3 pt-2">
                                @if ($order->order_comment !=null)
                                    <span class="font-weight-bold text-capitalize">
                                        {{translate('order_comment')}} :
                                    </span>
                                    <p class="pl-1">
                                        {{$order->order_comment}}
                                    </p>
                                @endif
                            </div>
                            <div class="col-6 pt-2">
                                <div class="text-right">
                                    <h6 class="" style="color: #8a8a8a;">
                                        {{translate('Payment')}} {{translate('Method')}}
                                        : {{str_replace('_',' ',$order['payment_method'])}}
                                    </h6>
                                    <h6 class="" style="color: #8a8a8a;">
                                        {{translate('Payment')}} {{translate('reference')}}
                                        : {{str_replace('_',' ',$order['transaction_ref'])}}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <form action="{{route('admin.orders.order-update-now',$order->id)}}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                    <div class="card-body">

                        @php($subtotal=0)
                        @php($total=0)
                        @php($shipping=0)
                        @php($discount=0)
                        @php($tax=0)
                      <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                       style="width: 60%; text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">
                        <thead class="thead-light">
                        <tr>
                            <th width="10%">{{translate('product')}}</th>
                            <th width="2%">{{translate('qty')}}</th>
                            <th width="5%">{{translate('price')}}</th>
                            <th width="40%">{{translate('Total')}}</th>
                            <th width="35%">{{translate('Action')}}</th>
                        </tr>
                        </thead>

                        <tbody id="body-table">
                            @foreach($order->details as $key=>$detail)
                                @php($subtotal=$detail['price']*$detail->qty+$detail['tax']-$detail['discount'])
                                @php($discount+=$detail['discount'])

                                    @php($tax+=$detail['tax'])
                                    @php($total+=$subtotal)
                                <tr>
                                <td width="20%">
                                    @if($detail->product)
                                                                    <p>
                                        {{substr($detail->product['name'],0,30)}}{{strlen($detail->product['name'])>10?'...':''}}</p>
                                    <strong><u>{{translate('Variation')}} : </u></strong>

                                    <div class="font-size-sm text-body">

                                        <span class="font-weight-bold">{{$detail['variant']}}</span>
                                    </div>
                                    @endif
                                </td>
                                    <td>
                                        <input type="text" class="qtyy"  name="qty[]" value="{{$detail->qty}}">
                                    </td>
                                    <td>
                                            {{$detail->price}}
                                    </td>
                                    <td class="sub_total">
                                            {{$detail->qty*$detail->price}}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{$detail->id}}" name="d_id[]">
                                        <input type="hidden" value="{{$detail->product_id}}" name="p_id[]">
                                        <input type="hidden" class="total_remove" value="{{$detail->qty*$detail->price}}}">
                                        <input type="hidden" name="variant_id[]" value="-1">
                                        <input type="hidden" class="price" value="{{$detail->price}}" name="price[]">
                                        <a href="#" class="btn btn-danger remove"><i class="tio-delete-outlined"></i></a>
                                    </td>


                                </tr>

                            @endforeach

                        </tbody>
                        </table>
                        @php($shipping=$order['shipping_cost'])
                        @php($discount=$order['discount_amount'])
                        @php($walletAmount=$order['walletAmount'])
                        @php($coupon_discount=$order['coupon_discount'])
                        @php($coupon_discount_type=$order['coupon_discount_type'])
                        @php($coupon_max_discount=$order['coupon_max_discount'])
                        {{-- <div>

                        </div> --}}
                        <div class="row justify-content-md-end mb-3">

                            @if($order['discount_amount'] == 0)
                            <div class="col-4 hs-unfold float-right pr-2">
                                <div class="dropdown">
                                    <select name="couponValue" class="couponValue form-control"
                                            data-id="{{$order['id']}}">
                                        <option>
                                            {{translate('select')}} {{translate('coupon')}}
                                        </option>
                                        @foreach($coupons as $coupon)
                                            <option coupon-max-discount="{{$coupon->max_discount}}" coupon-discount="{{$coupon->discount}}" coupon-discount-type="{{$coupon->discount_type}}" value="{{$coupon->id}}">{{$coupon->code}}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="row justify-content-md-end mb-3">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row text-sm-right">
                                    <dt class="col-sm-6">{{translate('sub_total')}}</dt>
                                    <dd class="col-sm-6 border-bottom">
                                        <strong id="subtotal">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total))}}</strong>
                                    </dd>
                                    <dt class="col-sm-6">{{translate('Shipping')}}</dt>
                                    <dd class="col-sm-6 border-bottom">
                                        <input type="hidden" value="{{$shipping}}" id="shipping">
                                        <strong>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping))}}</strong>
                                    </dd>

                                    <dt class="col-sm-6">{{translate('coupon_discount')}}</dt>
                                    <dd class="col-sm-6 border-bottom">
                                        <input type="hidden" value="{{$discount}}" id="discount">
                                        <input type="hidden" value="{{$coupon_discount}}" id="coupon_discount" name="coupon_discount">
                                        <input type="hidden" value="{{$coupon_discount_type}}" id="coupon_discount_type" name="coupon_discount_type">
                                        <input type="hidden" value="{{$coupon_max_discount}}" id="coupon_max_discount" name="coupon_max_discount">
                                        <strong id="discount_text">- {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($discount))}}</strong>
                                    </dd>
                                    <dt class="col-sm-6">{{translate('wallet_discount')}}</dt>
                                    <dd class="col-sm-6 border-bottom">
                                        <input type="hidden" value="{{$walletAmount}}" id="walletAmount">
                                        <strong>- {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($walletAmount))}}</strong>
                                    </dd>

                                    <dt class="col-sm-6">{{translate('Total')}}</dt>
                                    <dd class="col-sm-6">
                                        <input type="hidden" value="{{$total+$shipping-$discount-$walletAmount}}" id="total_hidden">
                                        <strong id="total">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total+$shipping-$discount-$walletAmount))}}</strong>
                                    </dd>
                                </dl>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success" type="submit">
                                {{translate('Update')}}
                         </button>
                    </div>
                    </form>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-4 border border-solid">
                <div class="row">

                        <select name="product_add" id="product" class="select2">
                        <option value="-1" selected>Select</option>
                        @foreach($products as $p)
                        <option value="{{$p->id}}">
                            {{$p->name}}
                        </option>
                        @endforeach
                        </select>


                </div>
                <div class="row mt-10">

                        <select name="variant_add" id="variant" class="select2">
                         <option value="-1" selected>Select</option>
                        </select>
                 </div>
                 <div class="row mt-10">
                        <button class="btn btn-success" id="add-item">
                        {{translate('Add')}}
                        </button>
                 </div>


            </div>
        </div>
        <!-- End Row -->
    </div>



@endsection

@push('script_2')
    <script>
        function calculateTotal(){
            subtotal = 0;
            for(var i = 0 ; i < $('.qtyy').length ; i++){
                var qtyy = $($('.qtyy')[i]).val();
                var price = $($('.price')[i]).val();
                subtotal += qtyy * price;
            }
            var discount = parseFloat($('#discount').val());
            var coupon_max_discount = parseFloat($('#coupon_max_discount').val());
            var coupon_discount = $('#coupon_discount').val();
            var coupon_discount_type = $('#coupon_discount_type').val();
            if(coupon_discount_type == "percentage"){
                discount = (subtotal * coupon_discount) / 100;
            }

            if(isNaN(discount)){
                discount = 0 ;
            }

            if(coupon_max_discount < discount){
                discount = coupon_max_discount;
            }
            var shipping = parseFloat($('#shipping').val());
            var walletAmount = parseFloat($('#walletAmount').val());

            var total = subtotal+shipping-discount-walletAmount;

            var  symbol = "{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(""))}}"

            $('#discount').val(discount);
            $('#discount_text').html("- "+symbol.replace("0.00"," "+discount.toFixed(2)));

            $('#total_hidden').val(total);
            $('#total').html(symbol.replace("0.00"," "+total.toFixed(2)));
            $('#subtotal').html(symbol.replace("0.00"," "+subtotal.toFixed(2)));


        }
        $(document).on('change', '.couponValue', function () {

            var coupon_discount = parseFloat($('.couponValue option:selected').attr('coupon-discount'));
            var coupon_discount_type = $('.couponValue option:selected').attr('coupon-discount-type') ;
            var coupon_max_discount = parseFloat($('.couponValue option:selected').attr('coupon-max-discount')) ;
            if(coupon_discount_type != "percentage"){
                $('#discount').val(coupon_discount);
            }
            $('#coupon_discount').val(coupon_discount);
            $('#coupon_discount_type').val(coupon_discount_type);
            $('#coupon_max_discount').val(coupon_max_discount);
            calculateTotal();

        })
        $(document).on('change', '.payment_status', function () {
            var id = $(this).attr("data-id");
            var value = $(this).val();
            Swal.fire({
                title: '{{translate('Are you sure Change this')}}?',
                text: "{{translate('You will not be able to revert this')}}!",
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{translate('Yes, Change it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.orders.payment-status')}}",
                        method: 'POST',
                        data: {
                            "id": id,
                            "payment_status": value
                        },
                        success: function (data) {
                            toastr.success('{{translate('Status Change successfully')}}');
                            location.reload();
                        }
                    });
                }
            })
        });

        function order_status(status) {
            var textarea = `<div>
                    <textarea  style="height: 120px;width: 100%;" id="order_comment">{{$order->order_comment}}</textarea>
                </div>`;
            @if($order['order_status']=='delivered')
            Swal.fire({
                title: '{{translate('Order is already delivered, and transaction amount has been disbursed, changing status can be the reason of miscalculation')}}!',
                html: "{{translate('Think before you proceed')}}." + textarea,
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{translate('Yes, Change it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.orders.status')}}",
                        method: 'POST',
                        data: {
                            "id": '{{$order['id']}}',
                            "order_status": status,
                            "order_comment": $('#order_comment').val()
                        },
                        success: function (data) {
                            if (data.success == 0) {
                                toastr.success('{{translate('Order is already delivered, You can not change it')}} !!');
                                location.reload();
                            } else {
                                if(data.payment_status == 0){
                                    toastr.warning('{{translate('Before delivered you need to make payment status paid!')}}!');
                                    location.reload();
                                }else{
                                    toastr.success('{{translate('Status Change successfully')}}!');
                                    location.reload();
                                }
                            }

                        }
                    });
                }
            })
            @else
            Swal.fire({
                title: '{{translate('Are you sure Change this')}}?',
                html: "{{translate('You will not be able to revert this')}}!" + textarea,
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{translate('Yes, Change it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.orders.status')}}",
                        method: 'POST',
                        data: {
                            "id": '{{$order['id']}}',
                            "order_status": status,
                            "order_comment": $('#order_comment').val()
                        },
                        success: function (data) {
                            if (data.success == 0) {
                                toastr.success('{{translate('Order is already delivered, You can not change it')}} !!');
                                location.reload();
                            } else {
                                if(data.payment_status == 0){
                                    toastr.warning('{{translate('Before delivered you need to make payment status paid!')}}!');
                                    location.reload();
                                }else{
                                    toastr.success('{{translate('Status Change successfully')}}!');
                                    location.reload();
                                }
                            }

                        }
                    });
                }
            })
            @endif
        }
    </script>
<script>
    $( document ).ready(function() {
        let delivery_type = '{{$order->delivery_type}}';

        $('.select2').select2({ width: '100%' });

        if(delivery_type === 'self_delivery'){
            $('#choose_delivery_man').show();
            $('#by_third_party_delivery_service_info').hide();
        }else if(delivery_type === 'third_party_delivery')
        {
            $('#choose_delivery_man').hide();
            $('#by_third_party_delivery_service_info').show();
        }else{
            $('#choose_delivery_man').hide();
            $('#by_third_party_delivery_service_info').hide();
        }
    });
</script>
<script>
    function choose_delivery_type(val)
    {

        if(val==='self_delivery')
        {
            $('#choose_delivery_man').show();
            $('#by_third_party_delivery_service_info').hide();
        }else if(val==='third_party_delivery'){
            $('#choose_delivery_man').hide();
            $('#by_third_party_delivery_service_info').show();
            $('#shipping_chose').modal("show");
        }else{
            $('#choose_delivery_man').hide();
            $('#by_third_party_delivery_service_info').hide();
        }

    }
</script>
    <script>
        function addDeliveryMan(id) {
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/admin/orders/add-delivery-man/{{$order['id']}}/' + id,
                data: {
                    'order_id': '{{$order['id']}}',
                    'delivery_man_id': id
                },
                success: function (data) {
                    if (data.status == true) {
                        toastr.success('Delivery man successfully assigned/changed', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    } else {
                        toastr.error('Deliveryman man can not assign/change in that status', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function () {
                    toastr.error('Add valid data', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function last_location_view() {
            toastr.warning('Only available when order is out for delivery!', {
                CloseButton: true,
                ProgressBar: true
            });
        }

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        function waiting_for_location() {
            toastr.warning('{{translate('waiting_for_location')}}', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{\App\CPU\Helpers::get_business_settings('map_api_key')}}&v=3.49"></script>
    <script>

        function initializegLocationMap() {
            var map = null;
            var myLatlng = new google.maps.LatLng({{isset($shipping_address->latitude) ? $shipping_address->latitude : 0}}, {{isset($shipping_address->longitude) ? $shipping_address->longitude : 0}});
            var dmbounds = new google.maps.LatLngBounds(null);
            var locationbounds = new google.maps.LatLngBounds(null);
            var dmMarkers = [];
            dmbounds.extend(myLatlng);
            locationbounds.extend(myLatlng);

            var myOptions = {
                center: myLatlng,
                zoom: 13,
                mapTypeId: google.maps.MapTypeId.ROADMAP,

                panControl: true,
                mapTypeControl: false,
                panControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_CENTER
                },
                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.RIGHT_CENTER
                },
                scaleControl: false,
                streetViewControl: false,
                streetViewControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_CENTER
                }
            };
            map = new google.maps.Map(document.getElementById("location_map_canvas"), myOptions);
            console.log(map);
            var infowindow = new google.maps.InfoWindow();

            @if($shipping_address && isset($shipping_address))
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng({{isset($shipping_address->latitude) ? $shipping_address->latitude : 0}}, {{isset($shipping_address->longitude) ? $shipping_address->longitude : 0}}),
                map: map,
                title: "{{$order->customer['f_name']??""}} {{$order->customer['l_name']??""}}",
                icon: "{{asset('public/assets/front-end/img/customer_location.png')}}"
            });

            google.maps.event.addListener(marker, 'click', (function (marker) {
                return function () {
                    infowindow.setContent("<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{asset('storage/app/public/profile/')}}{{$order->customer->image??""}}'></div><div style='float:right; padding: 10px;'><b>{{$order->customer->f_name??""}} {{$order->customer->l_name??""}}</b><br/>{{$shipping_address->address??""}}</div>");
                    infowindow.open(map, marker);
                }
            })(marker));
            locationbounds.extend(marker.getPosition());
            @endif

            google.maps.event.addListenerOnce(map, 'idle', function () {
                map.fitBounds(locationbounds);
            });
        }

        // Re-init map before show modal
        $('#locationModal').on('shown.bs.modal', function (event) {
            initializegLocationMap();
        });
    </script>
    <script>

    $(document).on('click','.remove',function(e){
        e.preventDefault();
        calculateTotal();
        // let rem=$(this).parent().find('.total_remove').val();
        // let total=parseFloat($('#total_hidden').val());
        // total-=parseFloat(rem).toFixed(2);
        // $('#total_hidden').val(total);
        // $('#total').html(total.toFixed(2));
        // $(this).parent().parent().remove();
    });

    $(document).on('change','.qtyy',function(e){
        e.preventDefault();
        calculateTotal();
        // let qty=$(this).val();
        // let price=$(this).parent().parent().find('.price').val();
        // console.log(price);
        // let rem=$(this).parent().parent().find('.total_remove').val();
        // $(this).parent().parent().find('.total_remove').val(price*qty);
        // let total=parseFloat($('#total_hidden').val());
        // total-=parseFloat(rem).toFixed(2);
        // total+=(price*qty);
        // $(this).parent().parent().find('.sub_total').html(price*qty);
        // $('#total_hidden').val(total);
        // $('#total').html(total.toFixed(2));

    });
    $('#product').on('change',function(){
                    let id=$(this).val();
                    $.ajax({
                        url: "{{route('admin.orders.product.variant')}}",
                        method: 'get',
                        data: {
                            "id": id,

                        },
                        success: function (data) {
                            console.log(data);
                            let html="";
                            $.each(data, function (key, val) {
                                   html+=" <option value='"+val.type+"'>"+val.type+" /"+val.price+"</option> ";
                            });
                            $('#variant').empty();
                             $('#variant').append(html);


                        }
                    });
    });

    $('#add-item').on('click',async function(){

        let product_id=$('#product').val();
        let variant=$('#variant').val();
        let price,product_name;
         await $.ajax({
                        url: "{{route('admin.orders.product.add')}}",
                        method: 'get',
                        data: {
                            "p_id": product_id,
                            "v_id":variant,
                        },
                        success: function (data) {
                            console.log(data);
                            price=data.variant.price;
                            product_name=data.product.name;
                                    calculateTotal();
                                    // let total=parseFloat($('#total_hidden').val());

                                    // total+=parseFloat(price);
                                    // $('#total_hidden').val(total);
                                    // $('#total').html(total.toFixed(2));


                        }
                    });
        let html="<tr>"
            html+="<td>";
            html+="<p>"+product_name+"</p>"
                 +'<input type="hidden" name="variant_id[]" value="'+variant+'">'
                +"<strong><u>{{translate('Variation')}} : </u></strong><div class='font-size-sm text-body'>"
                +"<span class='font-weight-bold'>"+variant+"</span></div>";
            html+="</td>"
            html+='<td><input type="text" class="qtyy" name="qty[]" value="1"></td>"';
            html+='<td>'+price+'</td>';
            html+='<td class="sub_total">'+price+'</td>';
            html+='<td><input type="hidden" value="-1" name="d_id[]">'
            +'<input type="hidden" value="'+product_id+'" name="p_id[]">'
            +'<input type="hidden" class="price" value="'+price+'" name="price[]">'
            +'<input type="hidden" class="total_remove" value="'+price+'">  '
            +'<a href="#" class="btn btn-danger remove"><i class="tio-delete-outlined"></i></a></td>';

        html+="</tr>";
        $('#body-table').append(html);

    });
    </script>
@endpush
