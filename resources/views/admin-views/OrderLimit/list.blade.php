@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Coupon Add'))

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
                    {{-- <h1 class="page-header-title"><i
                            class="tio-add-circle-outlined"></i> {{\App\CPU\translate('Add')}} {{\App\CPU\translate('New')}} {{\App\CPU\translate('Coupon')}}
                    </h1> --}}
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        
        

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row justify-content-between align-items-center flex-grow-1">
                            <div class="col-lg-3 mb-3 mb-lg-0">
                                <h5>
                                    {{\App\CPU\translate('orderLimit_table')}} <span style="color: red;">({{ $orderLimit->total() }})</span>
                                    <a href="{{route('admin.OrderLimit.manage')}}">
                                        <button class="btn btn-primary">
                                            {{\App\CPU\translate('add-new')}}
                                        </button>
                                    </a>
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
                                               placeholder="{{\App\CPU\translate('Search by Title or Code or Discount Type')}}"
                                               value="{{ $search }}" aria-label="Search orders" required>
                                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('search')}}</button>
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
                                    <th>{{\App\CPU\translate('SL')}}#</th>
                                    <th>{{\App\CPU\translate('cities')}}</th>
                                    <th>{{\App\CPU\translate('limit')}}</th>
                                    <th>{{\App\CPU\translate('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orderLimit as $k=>$_orderLimit)
                                <tr>
                                        <td >{{$orderLimit->firstItem() + $k}}</td>
                                        <td style="text-transform: capitalize;white-space: pre-wrap;">
                                            {{$_orderLimit->cities}}
                                        </td>
                                        <td>{{ $_orderLimit->Limit }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a class="btn btn-primary btn-sm edit m-1"
                                                href="{{route('admin.OrderLimit.manage',[$_orderLimit->id])}}"
                                                title="{{ \App\CPU\translate('Edit')}}"
                                                >
                                                <i class="tio-edit"></i>
                                            </a><br>
                                            <a class="btn btn-danger btn-sm delete m-1"
                                                href="javascript:"
                                                onclick="form_alert('coupon-{{$_orderLimit->id}}','Want to delete this coupon ?')"
                                                title="{{\App\CPU\translate('delete')}}"   
                                                >
                                                <i class="tio-add-to-trash"></i>
                                            </a>
                                            <form action="{{route('admin.OrderLimit.delete',[$_orderLimit->id])}}"
                                                method="post" id="coupon-{{$orderLimit['id']}}">
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
                        {{$orderLimit->links()}}
                    </div>
                    @if(count($orderLimit)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg"
                                 alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{\App\CPU\translate('No data to show')}}</p>
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
