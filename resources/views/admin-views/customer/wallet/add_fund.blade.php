@extends('layouts.back-end.app')
{{--@section('title','Customer')--}}
@section('title', \App\CPU\translate('customer_settings'))

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
                    <h1 class="page-header-title">{{\App\CPU\translate('add_fund')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="card gx-2 gx-lg-3">
            <div class="card-body">
                <form action="{{route('admin.customer.wallet.add-fund')}}" method="post" enctype="multipart/form-data" id="add_fund">
                    @csrf
                    <div class="row" style="align-items: center;">
                        <!--<div class="col-sm-1 col-12">-->
                        <!--    <div class="form-group" style="display: flex;align-items: end;margin: 0;padding: 0;">-->
                        <!--        <input type="checkbox" id='forAll' name="ForAll"  class="form-control" style="width: 50px;">-->
                        <!--        <label class="input-label" for="ForAll">{{\App\CPU\translate('ForAll')}}</label>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <div class="col-sm-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="customer">{{\App\CPU\translate('customer')}}</label>
                                <select id='customer' name="customer_id[]" multiple data-placeholder="{{\App\CPU\translate('select_customer')}}" class="js-data-example-ajax form-control">

                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="amount">{{\App\CPU\translate('amount')}}</label>
                        
                                <input type="number" class="form-control" name="amount" id="amount" step=".01" required>
                            </div>
                        </div>
                        <div class="col-sm-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="amount">{{\App\CPU\translate('expiredDate')}} <small>({{\App\CPU\translate('optional')}})</small></label>
                                <input type="date" class="form-control" name="expiredDate" id="expiredDate" >
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="input-label" for="referance">{{\App\CPU\translate('reference')}} <small>({{\App\CPU\translate('optional')}})</small></label>
                        
                                <input type="text" class="form-control" name="referance" id="referance">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="input-label" for="notification">{{\App\CPU\translate('notification')}} </label>
                        
                                <input type="text" class="form-control" name="notification" id="notification">
                            </div>
                        </div>
                
                
                        <div class="col-md-3 col-6">
                            <label>{{\App\CPU\translate('ForAll')}}</label>
                            <select class="form-control" id="ForAll" name="ForAll">
                                <option value="1">{{\App\CPU\translate('yes')}}</option>
                                <option value="0">{{\App\CPU\translate('no')}}</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-6 forAll">
                            <label>{{\App\CPU\translate('fromDateRegister')}}</label>
                            <input type="date" name="fromDate2" value=""  id="from_date"
                                    class="form-control" >
                        </div>
                        <div class="col-md-3 col-6 forAll">
                            <label>{{\App\CPU\translate('toDateRegister')}}</label>
                            <input type="date"  name="toDate2" value="" id="to_date"
                                    class="form-control" > 
                        </div>
                        <div class="col-md-3 col-6 forAll">
                            <label>{{\App\CPU\translate('fromOrder')}}</label>
                            <input type="number" name="fromOrder2" value=""  id="from_date"
                                    class="form-control" >
                        </div>
                        <div class="col-md-3 col-6 forAll">
                            <label>{{\App\CPU\translate('toOrder')}}</label>
                            <input type="number"  name="toOrder2" value="" id="to_date"
                            class="form-control" > 
                        </div>
                        <div class="col-md-3 col-6 forAll">
                            <label>{{\App\CPU\translate('fromOrderprice')}}</label>
                            <input type="number" name="fromOrderprice2" value=""  id="from_date"
                                    class="form-control" >
                        </div>
                        <div class="col-md-3 col-6 forAll">
                            <label>{{\App\CPU\translate('toOrderprice')}}</label>
                            <input type="number"  name="toOrderprice2" value="" id="to_date"
                            class="form-control" > 
                        </div>
                        <div class="col-md-3 col-6 forAll">
                            <label>{{\App\CPU\translate('city')}}</label>
                            <select multiple class="form-select form-control select-areas" name="city2[]" type="city" id="si-city"
                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                >
                                <option selected></option>
                                @foreach ($governorates as $governorate)
                                    <option value="{{$governorate->id}}">{{$governorate->governorate_name_ar}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-6 forAll">
                            <label>{{\App\CPU\translate('area')}}</label>
                            <select class="form-select form-control" name="area2" type="country" id="si-area"
                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                >
                                <option selected></option>
                                @foreach ($cities as $city)
                                    <option  data-parent="{{$city->governorate_id}}" value="{{$city->id}}">{{$city->city_name_ar}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-6 forAll">
                            <label>{{\App\CPU\translate('Type')}}</label>
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
                    <button type="submit" id="submit" class="btn btn-primary">{{\App\CPU\translate('submit')}}</button>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>
@endsection

@push('script_2')
<script>


        
        $(document).ready(function () {
            $('.select-areas').select2();
        });
            
        $('#si-city').on('change',function(){
            var city = $('#si-city option:selected').val();
            $('#si-area option').removeClass('city-active');
            $('#si-area option[ data-parent="'+city+'"]').addClass('city-active');
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

        $('#add_fund').on('submit', function (e) {
            
            e.preventDefault();
            var formData = new FormData(this);
            
            Swal.fire({
                title: '{{\App\CPU\translate('are_you_sure')}}',
                // text: '{{\App\CPU\translate('you_want_to_add_fund')}}'+$('#amount').val()+' {{\App\CPU\Helpers::currency_code().' '.\App\CPU\translate('to')}} '+$('#customer option:selected').text()+'{{\App\CPU\translate('to_wallet')}}',
                text: '{{\App\CPU\translate('you_want_to_add_fund')}}'+$('#amount').val(),
                type: 'info',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: 'primary',
                cancelButtonText: '{{\App\CPU\translate('no')}}',
                confirmButtonText: '{{\App\CPU\translate('add')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.post({
                        url: '{{route('admin.customer.wallet.add-fund')}}',
                        data: formData,
                        cache: false,
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
                                toastr.success('{{\App\CPU\translate("fund_added_successfully")}}', {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            }
                        }
                    });
                }
            })
        })

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{route('admin.customer.customer-list-search')}}',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                    results: data
                    };
                },
                __port: function (params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });
        
        $('#forAll').on('click',function(){
            var checked = $(this).prop('checked');
            if(checked){
                $('#customer').attr('disabled' , true);
            }else{
                $('#customer').attr('disabled' , false);
            }
        })
        
    </script>
@endpush