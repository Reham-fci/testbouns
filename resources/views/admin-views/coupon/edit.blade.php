@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Coupon Edit'))
@push('css_or_js')
    <link href="{{asset('public/assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>
    
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
    <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{\App\CPU\translate('Coupon')}} {{\App\CPU\translate('update')}}</h1>
                </div>
            </div>
        </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <form action="{{route('admin.coupon.update',[$c['id']])}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label  for="name">{{\App\CPU\translate('Type')}}</label>
                                    <select class="form-control" name="coupon_type"
                                            style="width: 100%" required>
                                        {{--<option value="delivery_charge_free">Delivery Charge Free</option>--}}
                                        <option value="discount_on_purchase" {{$c['coupon_type']=='discount_on_purchase'?'selected':''}}>{{\App\CPU\translate('Discount on Purchase')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">{{\App\CPU\translate('Title')}}</label>
                                    <input type="text" name="title" class="form-control" id="title" value="{{$c['title']}}"
                                        placeholder="{{\App\CPU\translate('Title')}}" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">{{\App\CPU\translate('Code')}}</label>
                                    <input type="text" name="code" value="{{$c['code']}}"
                                           class="form-control" id="code"
                                           placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 col-6">
                                <div class="form-group">
                                    <label for="name">{{\App\CPU\translate('start_date')}}</label>
                                    <input type="date" name="start_date" class="form-control" id="start_date" value="{{date('Y-m-d',strtotime($c['start_date']))}}"
                                        placeholder="{{\App\CPU\translate('start date')}}" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="form-group">
                                    <label for="name">{{\App\CPU\translate('expire_date')}}</label>
                                    <input type="date" name="expire_date" class="form-control" id="expire_date" value="{{date('Y-m-d',strtotime($c['expire_date']))}}"
                                           placeholder="{{\App\CPU\translate('expire date')}}" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="form-group">
                                    <label  for="exampleFormControlInput1">{{\App\CPU\translate('limit')}} {{\App\CPU\translate('for')}} {{\App\CPU\translate('same')}} {{\App\CPU\translate('user')}}</label>
                                        <input type="number" name="limit" value="{{ $c['limit'] }}" id="coupon_limit" class="form-control" placeholder="{{\App\CPU\translate('EX')}}: {{\App\CPU\translate('10')}}">
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="form-group">
                                    <label  for="name">{{\App\CPU\translate('discount_type')}}</label>
                                    <select id="discount_type" class="form-control" name="discount_type"
                                            onchange="checkDiscountType(this.value)"
                                            style="width: 100%">
                                        <option value="amount" {{$c['discount_type']=='amount'?'selected':''}}>{{\App\CPU\translate('Amount')}}</option>
                                        <option value="percentage" {{$c['discount_type']=='percentage'?'selected':''}}>{{\App\CPU\translate('percentage')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 col-6">
                                <div class="form-group">
                                    <label for="name">{{\App\CPU\translate('Discount')}}</label>
                                    <input type="number" min="0" max="1000000" step=".01" name="discount" class="form-control" id="discount" value="{{$c['discount_type']=='amount'?\App\CPU\Convert::default($c['discount']):$c['discount']}}"
                                           placeholder="{{\App\CPU\translate('discount')}}" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <label for="name">{{\App\CPU\translate('minimum_purchase')}}</label>
                                <input type="number" min="0" max="1000000" step=".01" name="min_purchase" class="form-control" id="minimum purchase" value="{{\App\CPU\Convert::default($c['min_purchase'])}}"
                                        placeholder="{{\App\CPU\translate('minimum purchase')}}" required>
                            </div>
                            <div id="max-discount" class="col-md-3 col-6">
                                <div class="form-group">
                                    <label for="name">{{\App\CPU\translate('maximum_discount')}}</label>
                                    <input type="number" min="0" max="1000000" step=".01" name="max_discount" class="form-control" id="maximum discount" value="{{\App\CPU\Convert::default($c['max_discount'])}}"
                                           placeholder="{{\App\CPU\translate('maximum discount')}}">
                                </div>
                            </div>
                        </div>





                        <div class="row">
                            <div class="col-md-3 col-6">
                                <label>{{\App\CPU\translate('ForAll')}}</label>
                                <select class="form-control" id="ForAll" name="forall" style="<?php if($c['forall'] == 0){echo "display:block";}?>">
                                    <option <?php if($c['forall'] == 1){echo "selected";}?> value="1">{{\App\CPU\translate('yes')}}</option>
                                    <option <?php if($c['forall'] == 0){echo "selected";}?> value="0">{{\App\CPU\translate('no')}}</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-6 forAll" style="<?php if($c['forall'] == 0){echo "display:block";}?>">
                                <label>{{\App\CPU\translate('fromDateRegister')}}</label>
                                <input type="date" name="fromDate2" value="{{$c['FromRegisterDate'] ? $c['FromRegisterDate'] : ""}}"  id="from_date"
                                        class="form-control" >
                            </div>
                            <div class="col-md-3 col-6 forAll" style="<?php if($c['forall'] == 0){echo "display:block";}?>">
                                <label>{{\App\CPU\translate('toDateRegister')}}</label>
                                <input type="date"  name="toDate2" value="{{$c['ToRegisterDate'] ? $c['ToRegisterDate'] : ""}}" id="to_date"
                                        class="form-control" > 
                            </div>
                            <div class="col-md-3 col-6 forAll" style="<?php if($c['forall'] == 0){echo "display:block";}?>">
                                <label>{{\App\CPU\translate('fromOrder')}}</label>
                                <input type="number" name="fromOrder2" value="{{$c['FromOrderTimes'] ? $c['FromOrderTimes'] : ""}}"  id="from_date"
                                        class="form-control" >
                            </div>
                            <div class="col-md-3 col-6 forAll" style="<?php if($c['forall'] == 0){echo "display:block";}?>">
                                <label>{{\App\CPU\translate('toOrder')}}</label>
                                <input type="number"  name="toOrder2" value="{{$c['ToOrderTimes'] ? $c['ToOrderTimes'] : ""}}" id="to_date"
                                class="form-control" > 
                            </div>
                            <div class="col-md-3 col-6 forAll" style="<?php if($c['forall'] == 0){echo "display:block";}?>">
                                <label>{{\App\CPU\translate('fromOrderprice')}}</label>
                                <input type="number" name="fromOrderprice2" value="{{$c['Fromprice'] ? $c['Fromprice'] : ""}}"  id="from_date"
                                        class="form-control" >
                            </div>
                            <div class="col-md-3 col-6 forAll" style="<?php if($c['forall'] == 0){echo "display:block";}?>">
                                <label>{{\App\CPU\translate('toOrderprice')}}</label>
                                <input type="number"  name="toOrderprice2" value="{{$c['Toprice'] ? $c['Toprice'] : ""}}" id="to_date"
                                class="form-control" > 
                            </div>
                            <div class="col-md-3 col-6 forAll" style="<?php if($c['forall'] == 0){echo "display:block";}?>">
                                <label>{{\App\CPU\translate('city')}}</label>
                                <select class="form-select form-control select-areas" name="city2[]" multiple type="city" id="si-city"
                                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                    >
                                    @foreach ($governorates as $governorate)
                                        <option {{ in_array($governorate->id, $c['cityList']) ? "selected" : "" }} value="{{$governorate->id}}">{{$governorate->governorate_name_ar}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-6 forAll" style="<?php if($c['forall'] == 0){echo "display:block";}?>">
                                <label>{{\App\CPU\translate('area')}}</label>
                                <select class="form-select form-control select-areas" name="area2[]" multiple type="country" id="si-area"
                                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                    >
                                    @foreach ($cities as $city)
                                    
                                        <option class="city-non-active {{in_array($city->governorate_id , $c['cityList']) ? "city-active" : "" }}" {{ in_array($city->id , $c['areaList']) ? "selected" : "" }}  data-parent="{{$city->governorate_id}}" value="{{$city->id}}">{{$city->city_name_ar}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-6 forAll" style="<?php if($c['forall'] == 0){echo "display:block";}?>">
                                <label>{{\App\CPU\translate('Type')}}</label>
                                <select class="form-select form-control" name="type2" type="type" id="si-Type"
                                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                    >
                                    <option selected></option>
                                    @foreach ($customertypes as $customertype)
                                        <option {{$c['type'] == $customertype->id ? "selected" : "" }} value="{{$customertype->id}}">{{$customertype->ar_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            
                            <div id="max-discount" class="col-md-3 col-6">
                                <div class="form-group">
                                    <label for="name">{{\App\CPU\translate('Quantity')}}</label>
                                    <input type="number" min="1" max="1000000" name="qty" 
                                           class="form-control" id="Quantity"
                                           value="{{$c['qty']}}"
                                           placeholder="{{\App\CPU\translate('Quantity')}}" >
                                </div>
                            </div>
                        </div>




                        <div class="">
                            <button type="submit" class="btn btn-primary float-right">{{\App\CPU\translate('Submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        
        $('#si-city').on('change',function(){
            var cities = $('#si-city option:selected');
            
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
        $(document).ready(function() {
                let discount_type = $('#discount_type').val();
                if (discount_type == 'amount') {
                    $('#max-discount').hide()
                } else if (discount_type == 'percentage') {
                    $('#max-discount').show()
                }
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
@endpush
