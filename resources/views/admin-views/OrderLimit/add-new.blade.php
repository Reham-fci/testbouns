@extends('layouts.back-end.app')

@section('title', translate('Coupon Add'))

@push('css_or_js')
    <link href="{{asset('public/assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>
@endpush
@push('css_or_js')
<style>
    .forAll{
        display: none;
    }

    .city-non-active{
            display: none;
    }
    .city-active{

        display: block;
    }
</style>
@endpush
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i
                            class="tio-add-circle-outlined"></i> {{translate('Add')}} {{translate('New')}} {{translate('Coupon')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Content Row -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.coupon.store-coupon')}}" method="post">
                            @csrf

                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="name">{{translate('Type')}}</label>
                                        <select class="form-control" name="coupon_type"
                                                style="width: 100%" required>
                                            {{--<option value="delivery_charge_free">Delivery Charge Free</option>--}}
                                            <option value="discount_on_purchase">{{translate('Discount_on_Purchase')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="name">{{translate('Title')}}</label>
                                        <input type="text" name="title" class="form-control" id="title"
                                               placeholder="{{translate('Title')}}" required>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="name">{{translate('Code')}}</label>
                                        <input type="text" name="code" value="{{\Illuminate\Support\Str::random(10)}}"
                                               class="form-control" id="code"
                                               placeholder="" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('start_date')}}</label>
                                        <input id="start_date" type="date" name="start_date" class="form-control"
                                               placeholder="{{translate('start date')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('expire_date')}}</label>
                                        <input id="expire_date" type="date" name="expire_date" class="form-control"
                                               placeholder="{{translate('expire date')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label
                                            for="exampleFormControlInput1">{{translate('limit')}} {{translate('for')}} {{translate('same')}} {{translate('user')}}</label>
                                        <input type="number" name="limit" id="coupon_limit" class="form-control"
                                               placeholder="{{translate('EX')}}: {{translate('10')}}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('discount_type')}}</label>
                                        <select id="discount_type" class="form-control" name="discount_type"
                                                onchange="checkDiscountType(this.value)"
                                                style="width: 100%">
                                            <option value="amount">{{translate('Amount')}}</option>
                                            <option value="percentage">{{translate('percentage')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('Discount')}}</label>
                                        <input type="number" step="any" min="1" max="1000000" name="discount" class="form-control"
                                               id="discount"
                                               placeholder="{{translate('discount')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label for="name">{{translate('minimum_purchase')}}</label>
                                    <input type="number" min="1" max="1000000" name="min_purchase" class="form-control"
                                           id="minimum purchase"
                                           placeholder="{{translate('minimum purchase')}}" required>
                                </div>
                                <div id="max-discount" class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('maximum_discount')}}</label>
                                        <input type="number" min="1" max="1000000" name="max_discount"
                                               class="form-control" id="maximum discount"
                                               placeholder="{{translate('maximum discount')}}" >
                                    </div>
                                </div>

                                <div id="max-discount" class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('Quantity')}}</label>
                                        <input type="number" min="1" max="1000000" name="qty"
                                               class="form-control" id="Quantity"
                                               placeholder="{{translate('Quantity')}}" >
                                    </div>
                                </div>


                                <div class="col-md-3 col-6">
                                    <label>{{translate('ForAll')}}</label>
                                    <select class="form-control" id="ForAll" name="ForAll">
                                        <option value="1">{{translate('yes')}}</option>
                                        <option value="0">{{translate('no')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6 forAll">
                                    <label>{{translate('fromDateRegister')}}</label>
                                    <input type="date" name="fromDate2" value="{{$seaerchData['fromDate'] ? $seaerchData['fromDate'] : ""}}"  id="from_date"
                                            class="form-control" >
                                </div>
                                <div class="col-md-3 col-6 forAll">
                                    <label>{{translate('toDateRegister')}}</label>
                                    <input type="date"  name="toDate2" value="{{$seaerchData['toDate'] ? $seaerchData['toDate'] : ""}}" id="to_date"
                                            class="form-control" >
                                </div>
                                <div class="col-md-3 col-6 forAll">
                                    <label>{{translate('fromOrder')}}</label>
                                    <input type="number" name="fromOrder2" value=""  id="from_date"
                                            class="form-control" >
                                </div>
                                <div class="col-md-3 col-6 forAll">
                                    <label>{{translate('toOrder')}}</label>
                                    <input type="number"  name="toOrder2" value="" id="to_date"
                                    class="form-control" >
                                </div>
                                <div class="col-md-3 col-6 forAll">
                                    <label>{{translate('fromOrderprice')}}</label>
                                    <input type="number" name="fromOrderprice2" value=""  id="from_date"
                                            class="form-control" >
                                </div>
                                <div class="col-md-3 col-6 forAll">
                                    <label>{{translate('toOrderprice')}}</label>
                                    <input type="number"  name="toOrderprice2" value="" id="to_date"
                                    class="form-control" >
                                </div>
                                <div class="col-md-3 col-6 forAll">
                                    <label>{{translate('city')}}</label>
                                    <select class="form-select form-control select-areas" name="city2[]" multiple type="city" id="si-city"
                                        style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                        >
                                        <option ></option>
                                        @foreach ($governorates as $governorate)
                                            <option value="{{$governorate->id}}">{{$governorate->governorate_name_ar}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 col-6 forAll">
                                    <label>{{translate('area')}}</label>
                                    <select class="form-select form-control select-areas" name="area2[]" multiple type="country" id="si-area"
                                        style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                        >
                                        <option ></option>
                                        @foreach ($cities as $city)
                                            <option class="city-non-active" data-parent="{{$city->governorate_id}}" value="{{$city->id}}">{{$city->city_name_ar}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 col-6 forAll">
                                    <label>{{translate('Type')}}</label>
                                    <select class="form-select form-control" name="type2" type="type" id="si-Type"
                                        style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                        >
                                        <option selected></option>
                                        @foreach ($customertypes as $customertype)
                                            <option  value="{{$customertype->id}}">{{$customertype->ar_name}}</option>
                                        @endforeach
                                    </select>
                                </div>



                            </div>

                            <div class="">
                                <button type="submit" class="btn btn-primary float-right">{{translate('Submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row justify-content-between align-items-center flex-grow-1">
                            <div class="col-lg-3 mb-3 mb-lg-0">
                                <h5>{{translate('coupons_table')}} <span style="color: red;">({{ $cou->total() }})</span>
                                </h5>
                            </div>
                            <div class="col-lg-6">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                               placeholder="{{translate('Search by Title or Code or Discount Type')}}"
                                               value="{{ $search }}" aria-label="Search orders" required>
                                        <button type="submit" class="btn btn-primary">{{translate('search')}}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table id="datatable"
                                   class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                   style="width: 100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{translate('SL')}}#</th>
                                    <th>{{translate('coupon_type')}}</th>
                                    <th>{{translate('Title')}}</th>
                                    <th>{{translate('Code')}}</th>
                                    <th>{{ translate('user') }} {{ translate('limit') }}</th>
                                    <th>{{translate('minimum_purchase')}}</th>
                                    <th>{{translate('maximum_discount')}}</th>
                                    <th>{{translate('Discount')}}</th>
                                    <th>{{translate('discount_type')}}</th>
                                    <th>{{translate('start_date')}}</th>
                                    <th>{{translate('expire_date')}}</th>
                                    <th>{{translate('Status')}}</th>
                                    <th>{{translate('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cou as $k=>$c)
                                    <tr>
                                        <td >{{$cou->firstItem() + $k}}</td>
                                        <td style="text-transform: capitalize">{{str_replace('_',' ',$c['coupon_type'])}}</td>
                                        <td class="text-capitalize">
                                            {{substr($c['title'],0,20)}}
                                        </td>
                                        <td>{{$c['code']}}</td>
                                        <td>{{ $c['limit'] }}</td>
                                        <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($c['min_purchase']))}}</td>
                                        <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($c['max_discount']))}}</td>
                                        <td>{{$c['discount_type']=='amount'?\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($c['discount'])):$c['discount']}}</td>
                                        <td>{{$c['discount_type']}}</td>
                                        <td>{{date('d-M-y',strtotime($c['start_date']))}}</td>
                                        <td>{{date('d-M-y',strtotime($c['expire_date']))}}</td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm">
                                                <input type="checkbox" class="toggle-switch-input"
                                                       onclick="location.href='{{route('admin.coupon.status',[$c['id'],$c->status?0:1])}}'"
                                                       class="toggle-switch-input" {{$c->status?'checked':''}}>
                                                <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a class="btn btn-primary btn-sm edit m-1"
                                                href="{{route('admin.coupon.update',[$c['id']])}}"
                                                title="{{ translate('Edit')}}"
                                                >
                                                <i class="tio-edit"></i>
                                            </a><br>
                                            <a class="btn btn-danger btn-sm delete m-1"
                                                href="javascript:"
                                                onclick="form_alert('coupon-{{$c['id']}}','Want to delete this coupon ?')"
                                                title="{{translate('delete')}}"
                                                >
                                                <i class="tio-add-to-trash"></i>
                                            </a>
                                            <form action="{{route('admin.coupon.delete',[$c['id']])}}"
                                                method="post" id="coupon-{{$c['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{$cou->links()}}
                    </div>
                    @if(count($cou)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg"
                                 alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{translate('No data to show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

<script>

    $('#btn-export').on('click',function(e){
        e.preventDefault();

        var search = $('[name="search"]').val();

        search = (typeof(search) == "undefined") ? "" : search;

        var fromDate = $('[name="fromDate"]').val();

        fromDate = (typeof(fromDate) == "undefined") ? "" : fromDate;

        var toDate = $('[name="toDate"]').val();

        toDate = (typeof(toDate) == "undefined") ? "" : toDate;

        var fromOrder = $('[name="fromOrder"]').val();

        fromOrder = (typeof(fromOrder) == "undefined") ? "" : fromOrder;

        var toOrder = $('[name="toOrder"]').val();

        toOrder = (typeof(toOrder) == "undefined") ? "" : toOrder;

        var fromOrderprice = $('[name="fromOrderprice"]').val();

        fromOrderprice = (typeof(fromOrderprice) == "undefined") ? "" : fromOrderprice;

        var toOrderprice = $('[name="toOrderprice"]').val();

        toOrderprice = (typeof(toOrderprice) == "undefined") ? "" : toOrderprice;

        var city = $('[name="city"]').val();

        city = (typeof(city) == "undefined") ? "" : city;

        var area = $('[name="area"]').val();

        area = (typeof(area) == "undefined") ? "" : area;

        var type = $('[name="type"]').val();

        type = (typeof(type) == "undefined") ? "" : type;
        var url = `search=`+search+`&fromDate=`+fromDate+`&toDate=`+toDate+`&fromOrder=`+fromOrder+`&toOrder=`+toOrder+`&fromOrderprice=`+fromOrderprice+`&toOrderprice=`+toOrderprice+`&city=`+city+`&area=`+area+`&type=`+type;
        console.log("<?=URL::to('admin/customer/export');?>"+url );
        open("<?=URL::to('admin/coupon/export?');?>"+url , '_blank');
    })
    $('#si-city').on('change',function(){
        var cities = $('#si-city option:selected');
        $('#si-area option').removeClass('city-active');
        for (let index = 0; index < cities.length; index++) {
            var city = $(cities[index]).val();
            $('#si-area option[ data-parent="'+city+'"]').addClass('city-active');
        }
    })

    $('#si-city2').on('change',function(){
        var city = $('#si-city2 option:selected').val();
        $('#si-area2 option').removeClass('city-active');
        $('#si-area2 option[ data-parent="'+city+'"]').addClass('city-active');
    })

    $('#ForAll').on('change',function(){
        if($(this).val() == 1){
            $('.forAll').hide();
        }
        else{
            $('.forAll').show();
        }
    })
</script>
<script>
    $(document).ready(function() {
            let discount_type = $('#discount_type').val();
            if (discount_type == 'amount') {
                $('#max-discount').hide()
            } else if (discount_type == 'percentage') {
                $('#max-discount').show()
            }
            //console.log(discount_type);
            $('#start_date').attr('min',(new Date()).toISOString().split('T')[0]);
            $('#expire_date').attr('min',(new Date()).toISOString().split('T')[0]);
        });

        $("#start_date").on("change", function () {
            $('#expire_date').attr('min',$(this).val());
        });

        $("#expire_date").on("change", function () {
            $('#start_date').attr('max',$(this).val());
        });

        function checkDiscountType(val) {
            if (val == 'amount') {
                $('#max-discount').hide()
            } else if (val == 'percentage') {
                $('#max-discount').show()
            }
        }


</script>

    <script src="{{asset('public/assets/back-end')}}/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select-areas').select2();
        });
        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>

    <!-- Page level plugins -->
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="{{asset('public/assets/back-end')}}/js/demo/datatables-demo.js"></script>
@endpush
