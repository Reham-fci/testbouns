@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Customer List'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 23px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 15px;
        width: 15px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #377dff;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #377dff;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    #banner-image-modal .modal-content {
        width: 1116px !important;
        margin-left: -264px !important;
    }

    @media (max-width: 768px) {
        #banner-image-modal .modal-content {
            width: 698px !important;
            margin-left: -75px !important;
        }


    }

    @media (max-width: 375px) {
        #banner-image-modal .modal-content {
            width: 367px !important;
            margin-left: 0 !important;
        }

    }

    @media (max-width: 500px) {
        #banner-image-modal .modal-content {
            width: 400px !important;
            margin-left: 0 !important;
        }


    }
    .city-non-active{
        display: none;
    }
    .city-active{
        
        display: block;
    }
    li.select2-selection__choice {
        color: #000 !important;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center mb-3">
            <div class="col-sm">
                <h1 class="page-header-title">{{\App\CPU\translate('Customer')}}
                    <span class="badge badge-soft-dark ml-2">{{\App\User::count()}}</span>
                    <a href="{{route('admin.customer.manage')}}">
                        <button class="btn btn-primary">
                            {{ \App\CPU\translate('add-new')}}
                        </button>
                    </a>
                </h1>
            </div>
        </div>
        <!-- End Row -->

        <!-- Nav Scroller -->
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <span class="hs-nav-scroller-arrow-prev" style="display: none;">
                <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                    <i class="tio-chevron-left"></i>
                </a>
            </span>

            <span class="hs-nav-scroller-arrow-next" style="display: none;">
                <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                    <i class="tio-chevron-right"></i>
                </a>
            </span>

            <!-- Nav -->
            <ul class="nav nav-tabs page-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#">{{\App\CPU\translate('Customer')}} {{\App\CPU\translate('List')}} </a>
                </li>
            </ul>
            <!-- End Nav -->
        </div>
        <!-- End Nav Scroller -->
    </div>
    <!-- End Page Header -->

    <!-- Card -->
    <div class="card">
        <!-- Header -->
        <div class="card-header">
            <div class="flex-between row justify-content-between align-items-center flex-grow-1 mx-1">
                <div>
                    <div class="flex-start">
                        <div>
                            <h5>{{ \App\CPU\translate('Customer')}} {{ \App\CPU\translate('Table')}}</h5>
                        </div>
                        <div class="mx-1">
                            <h5 style="color: red;">({{ $customers->total() }})</h5>
                        </div>
                    </div>
                </div>
                <div style="width: 40vw">
                    <!-- Search -->
                    {{-- <form action="{{ url()->current() }}" method="GET">
                        <div class="input-group input-group-merge input-group-flush">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">{{\App\CPU\translate('search')}}</button>
                        </div>
                    </form> --}}
                    <!-- End Search -->
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Header -->

        <div class="clearfix"></div>
        <div class="row">
            <div class="col-12 col-md-12 mt-2 mt-sm-0">
                <form action="{{ url()->current() }}" method="GET">
                    
                    <div class="row">
                        
                        <div class="col-2">
                            <label>{{\App\CPU\translate('Search by Name or Email or Phone')}}</label>
                            <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{\App\CPU\translate('Search by Name or Email or Phone')}}" aria-label="Search orders" value="{{ $search }}">
                        </div>
                        <div class="col-2">
                            <label>{{\App\CPU\translate('fromDate')}}</label>
                            <input type="date" name="fromDate" value="{{$seaerchData['fromDate'] ? $seaerchData['fromDate'] : ""}}"  id="from_date"
                                    class="form-control" >
                        </div>
                        <div class="col-2 mt-2 mt-sm-0">
                            <label>{{\App\CPU\translate('toDate')}}</label>
                            <input type="date"  name="toDate" value="{{$seaerchData['toDate'] ? $seaerchData['toDate'] : ""}}" id="to_date"
                                    class="form-control" > 
                        </div>
                        <div class="col-2">
                            <label>{{\App\CPU\translate('fromOrder')}}</label>
                            <input type="number" name="fromOrder" value="{{$seaerchData['fromOrder'] ? $seaerchData['fromOrder'] : ""}}"  id="from_date"
                                    class="form-control" >
                        </div>
                        <div class="col-2 mt-2 mt-sm-0">
                            <label>{{\App\CPU\translate('toOrder')}}</label>
                            <input type="number"  name="toOrder" value="{{$seaerchData['toOrder'] ? $seaerchData['toOrder'] : ""}}" id="to_date"
                            class="form-control" > 
                        </div>
                        <div class="col-2">
                            <label>{{\App\CPU\translate('fromOrderprice')}}</label>
                            <input type="number" name="fromOrderprice" value="{{$seaerchData['fromOrderprice'] ? $seaerchData['fromOrder'] : ""}}"  id="from_date"
                                    class="form-control" >
                        </div>
                        <div class="col-2 mt-2 mt-sm-0">
                            <label>{{\App\CPU\translate('toOrderprice')}}</label>
                            <input type="number"  name="toOrderprice" value="{{$seaerchData['toOrderprice'] ? $seaerchData['toOrder'] : ""}}" id="to_date"
                            class="form-control" > 
                        </div>
                        <div class="col-2 mt-2 mt-sm-0">
                            <label>{{\App\CPU\translate('city')}}</label>
                            <select class="form-select form-control city-select-area" name="city[]" type="city" id="si-city"
                            multiple style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                >
                                <option></option>
                                @foreach ($governorates as $governorate)
                                    <option {{(  in_array($governorate->id ,$seaerchData['city'] ) ) ? "selected" : ""}} value="{{$governorate->id}}">{{$governorate->governorate_name_ar}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2 mt-2 mt-sm-0">
                            <label>{{\App\CPU\translate('area')}}</label>
                            <select class="form-select form-control city-select-area" name="area" type="country" id="si-area"
                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                >
                                <option selected></option>
                                @foreach ($cities as $city)
                                    <option {{($seaerchData['area'] == $city->id) ? "selected" : ""}} 
                                        {{-- class="city-non-active {{(  in_array($city->governorate_id ,$seaerchData['city'] ) ) ? "city-active" : ""}}"  --}}
                                        data-parent="{{$city->governorate_id}}" value="{{$city->id}}">{{$city->city_name_ar}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2 mt-2 mt-sm-0">
                            <label>{{\App\CPU\translate('Type')}}</label>
                            <select class="form-select form-control" name="type" type="type" id="si-Type"
                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                >
                                <option selected></option>
                                @foreach ($customertypes as $customertype)
                                    <option {{($seaerchData['type'] == $customertype->id) ? "selected" : ""}} value="{{$customertype->id}}">{{$customertype->ar_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2 mt-2 mt-sm-0">
                            <label>
                                    {{\App\CPU\translate('Salesperson')}}
                            </label>
                            <select class="form-select form-control" name="salesPersonId" type="salesPersonId" id="si-salesPersonId"
                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                >
                                <option selected></option>
                                @foreach ($Salesperson as $_Salesperson)
                                    <option {{($seaerchData['salesPersonId'] == $_Salesperson->id) ? "selected" : ""}} value="{{$_Salesperson->id}}">{{$_Salesperson->f_name.' '.$_Salesperson->l_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-2  ">
                            <button type="submit" class="w-100 mt-5  btn btn-primary float-right float-sm-none">{{\App\CPU\translate('filter')}}</button>
                        </div>
                        <div class="col-2  ">
                            <button id="btn-export" class="w-100 mt-5  btn btn-primary float-right float-sm-none btn-export">{{\App\CPU\translate('export')}}</button>
                        </div>
                    </div>
                </form>
                
            </div>
        </div>
        <!-- Table -->
        <div class="table-responsive datatable-custom">
            <table style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table" style="width: 100%" data-hs-datatables-options='{
                     "columnDefs": [{
                        "targets": [0],
                        "orderable": false
                      }],
                     "order": [],
                     "info": {
                       "totalQty": "#datatableWithPaginationInfoTotalQty"
                     },
                     "search": "#datatableSearch",
                     "entries": "#datatableEntries",
                     "pageLength": 25,
                     "isResponsive": false,
                     "isShowPaging": false,
                     "pagination": "datatablePagination"
                   }'>
                <thead class="thead-light">
                    <tr>
                        <th class="">
                            #
                        </th>
                        <th class="table-column-pl-0">{{\App\CPU\translate('Name')}}</th>
                        <th class="table-column-pl-0">{{\App\CPU\translate('last_name')}}</th>
                        <th>{{\App\CPU\translate('Email')}}</th>
                        <th>{{\App\CPU\translate('Phone')}}</th>
                        <th>{{\App\CPU\translate('Salesperson')}}</th>
                        <th>{{\App\CPU\translate('city')}}</th>
                        <th>{{\App\CPU\translate('area')}}</th>
                        <th>{{\App\CPU\translate('Type')}}</th>
                        <th>{{\App\CPU\translate('getFrom')}}</th>
                        <th>{{\App\CPU\translate('RegisterDate')}}</th>
                        <th>{{\App\CPU\translate('Total')}} {{\App\CPU\translate('Order')}} </th>
                        <th>{{\App\CPU\translate('seller_amount')}}  </th>
                        <th>{{\App\CPU\translate('block')}} / {{\App\CPU\translate('unblock')}}</th>
                        <th>{{\App\CPU\translate('Action')}}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($customers as $key=>$customer)
                    <tr class="">
                        <td class="">
                            {{$customers->firstItem()+$key}}
                        </td>
                        <td class="table-column-pl-0">
                            <a href="{{route('admin.customer.view',[$customer['id']])}}">
                                {{$customer['f_name']}}
                            </a>
                        </td>
                        <td>
                            {{$customer['l_name']}}
                        </td>
                        <td>
                            {{$customer['email']}}
                        </td>
                        <td>
                            {{$customer['phone']}}
                        </td>
                        <td>
                            {{isset($customer['salesPerson']) ? $customer['salesPerson']->f_name." ".$customer['salesPerson']->l_name : ""}}
                        </td>
                        <td>
                            {{ isset($customer['cityName']->governorate_name_ar) ? $customer['cityName']->governorate_name_ar : "" }}
                        </td>
                        <td>
                            {{ isset($customer['areaName']->city_name_ar) ? $customer['areaName']->city_name_ar : "" }}
                        </td>
                        <td>
                            {{ isset($customer['_type']->ar_name) ? $customer['_type']->ar_name : "" }}
                        </td>
                        <td>
                            {{\App\CPU\translate($customer['getFrom'])}}
                        </td>
                        <td>
                            {{$customer['created_at']}}
                        </td>
                        <td>
                            <label class="badge badge-soft-info">
                                {{$customer->orders->count()}}
                            </label>
                        </td>
                        <td>
                            <label class="badge badge-soft-info">
                                {{$customer->orders->sum('order_amount')}}
                            </label>
                        </td>

                        <td>
                            <label class="switch">
                                <input type="checkbox" class="status" id="{{$customer['id']}}" {{$customer->is_active == 1?'checked':''}}>
                                <span class="slider round"></span>
                            </label>
                        </td>

                        <td>

                            <a title="{{\App\CPU\translate('View')}}" class="btn btn-info btn-sm" href="{{route('admin.customer.view',[$customer['id']])}}">
                                <i class="tio-visible"></i>
                            </a>
                            <a title="{{\App\CPU\translate('View')}}" class="btn btn-info btn-sm" href="{{route('admin.customer.manage',[$customer['id']])}}">
                                {{-- <i class="tio-visible"></i> --}}
                                {{-- <i class="fa-solid fa-pen-to-square"></i> --}}
                                <i class="tio-edit"></i>
                            </a>
                            <a title="{{\App\CPU\translate('delete')}}" class="btn btn-danger btn-sm delete" href="javascript:" onclick="form_alert('customer-{{$customer['id']}}','Want to delete this customer ?')">
                                <i class="tio-delete"></i>
                            </a>
                            <form action="{{route('admin.customer.delete',[$customer['id']])}}" method="post" id="customer-{{$customer['id']}}">
                                @csrf @method('delete')
                            </form>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- End Table -->

        <!-- Footer -->
        <div class="card-footer">
            {!! $customers->links() !!}
        </div>
        @if(count($customers)==0)
        <div class="text-center p-4">
            <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
            <p class="mb-0">{{\App\CPU\translate('No data to show')}}</p>
        </div>
        @endif
        <!-- End Footer -->
    </div>
    <!-- End Card -->
</div>
@endsection

@push('script_2')
<script>
    
    $(document).ready(function () {
        $('.city-select-area').select2();
    });
    $('#si-city').on('change',function(){
        var city = $('#si-city option:selected').val();
        $('#si-area option').removeClass('city-active');
        $('#si-area option[ data-parent="'+city+'"]').addClass('city-active');
    })
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
        
        // var city = $('[name="city"]').val();
        var cities = $('[name="city[]"]').val();
        
        cities = (typeof(cities) == "undefined") ? [] : cities;

        var city = "&city=-1,";
        for (let index = 0; index < cities.length; index++) {
            city += cities[index]+",";
        }
        city = city.substring(0, city.length - 1)+'&';
        
        var area = $('[name="area"]').val();
        
        area = (typeof(area) == "undefined") ? "" : area;
        
        var type = $('[name="type"]').val();
        
        
        type = (typeof(type) == "undefined") ? "" : type;
        var salesPersonId = $('[name="salesPersonId"]').val();
        salesPersonId = (typeof(salesPersonId) == "undefined") ? "" : salesPersonId;
        var url = `search=`+search+`&fromDate=`+fromDate+`&toDate=`+toDate+`&fromOrder=`+fromOrder+`&toOrder=`+toOrder+`&fromOrderprice=`+fromOrderprice+`&toOrderprice=`+toOrderprice+city+`area=`+area+`&type=`+type+`&salesPersonId=`+salesPersonId;
        console.log("<?=URL::to('admin/customer/export');?>"+url );
        open("<?=URL::to('admin/customer/export?');?>"+url , '_blank');
    })

</script>
<script>
    $(document).on('ready', function() {
        // INITIALIZATION OF DATATABLES
        // =======================================================
        var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy',
                    className: 'd-none'
                },
                {
                    extend: 'excel',
                    className: 'd-none'
                },
                {
                    extend: 'csv',
                    className: 'd-none'
                },
                {
                    extend: 'pdf',
                    className: 'd-none'
                },
                {
                    extend: 'print',
                    className: 'd-none'
                },
            ],
            select: {
                style: 'multi',
                selector: 'td:first-child input[type="checkbox"]',
                classMap: {
                    checkAll: '#datatableCheckAll',
                    counter: '#datatableCounter',
                    counterInfo: '#datatableCounterInfo'
                }
            },

        });

        $('#datatableSearch').on('mouseup', function(e) {
            var $input = $(this),
                oldValue = $input.val();

            if (oldValue == "") return;

            setTimeout(function() {
                var newValue = $input.val();

                if (newValue == "") {
                    // Gotcha
                    datatable.search('').draw();
                }
            }, 1);
        });
    });
</script>

<script>
    $(document).on('change', '.status', function() {
        var id = $(this).attr("id");
        if ($(this).prop("checked") == true) {
            var status = 1;
        } else if ($(this).prop("checked") == false) {
            var status = 0;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('admin.customer.status-update')}}",
            method: 'POST',
            data: {
                id: id,
                status: status
            },
            success: function() {
                toastr.success('{{\App\CPU\translate('
                    Status updated successfully ')}}');
            }
        });
    });
    
</script>
@endpush