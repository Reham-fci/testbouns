@extends('layouts.back-end.app-seller')

@section('title', translate('Add Shipping'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('seller.dashboard.index')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('shipping_method')}}</li>

            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card" style="height: 200px;">
                    <div class="card-header text-capitalize">
                        <h4>{{translate('choose_shipping_method')}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 text-capitalize" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                <select class="form-control text-capitalize" name="shippingCategory" onchange="seller_shipping_type(this.value);"
                                            style="width: 100%">
                                    <option value="0" selected disabled>---{{translate('select')}}---</option>
                                    <option value="order_wise" {{$shippingType=='order_wise'?'selected':'' }} >{{translate('order_wise')}} </option>
                                    <option  value="category_wise" {{$shippingType=='category_wise'?'selected':'' }} >{{translate('category_wise')}}</option>
                                    <option  value="product_wise" {{$shippingType=='product_wise'?'selected':'' }}>{{translate('product_wise')}}</option>
                                </select>
                            </div>
                            <div class="col-12 mt-2" id="product_wise_note">
                                <p class="m-2" style="color: red;">{{translate('note')}}: {{translate("Please_make_sure_all_the product's_delivery_charges_are_up_to_date.")}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content Row -->
        <div id="order_wise_shipping">
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h1 class="h3 mb-0 text-black-50 text-capitalize">{{translate('add_order_wise_shipping')}} </h1>
                        </div>
                        <div class="card-body">
                            <form action="{{route('seller.business-settings.shipping-method.add')}}" method="post"
                                  style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="title">{{translate('title')}}</label>
                                            <input type="text" name="title" class="form-control" placeholder="">
                                        </div>

                                        <div class="col-md-3">
                                            <label for="duration">{{translate('duration')}}</label>
                                            <input type="text" name="duration" class="form-control"
                                                   placeholder="{{translate('Ex')}} : 4-6 {{translate('days')}}">
                                        </div>

                                        <div class="col-md-3">
                                            <label for="cost">{{translate('cost')}}</label>
                                            <input type="number" min="0" max="1000000" name="cost" class="form-control" placeholder="{{translate('Ex')}} : 10 $">
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer" style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 0">
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
                            <h5 class="text-capitalize">{{translate('order_wise_shipping_method')}}  <span style="color: red;">({{ $shipping_methods->total() }})</span></h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                   class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                   style="width: 100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{translate('sl#')}}</th>
                                    <th>{{translate('title')}}</th>
                                    <th>{{translate('duration')}}</th>
                                    <th>{{translate('cost')}}</th>
                                    <th>{{translate('status')}}</th>
                                    <th scope="col" style="width: 50px">{{translate('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($shipping_methods as $k=>$method)
                                    <tr>
                                        <th scope="row">{{$shipping_methods->firstItem()+$k}}</th>
                                        <td>{{$method['title']}}</td>
                                        <td>
                                            {{$method['duration']}}
                                        </td>
                                        <td>
                                            {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($method['cost']))}}
                                        </td>

                                        <td>
                                            <label class="switch">
                                                    <input type="checkbox" class="status"
                                                           id="{{$method['id']}}" {{$method->status == 1?'checked':''}}>
                                                    <span class="slider round"></span>
                                                </label>
                                        </td>
                                        <td>

                                            <a  class="btn btn-primary btn-sm"
                                                title="{{translate('Edit')}}"
                                                href="{{route('seller.business-settings.shipping-method.edit',[$method['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a  class="btn btn-danger btn-sm delete"
                                                title="{{translate('Delete')}}"
                                                style="cursor: pointer;"
                                                id="{{ $method['id'] }}">
                                                <i class="tio-add-to-trash"></i>
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                        <div class="card-footer">
                        {!! $shipping_methods->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-2" id="update_category_shipping_cost">
            <div class="card-header text-capitalize">
                <h4>{{translate('update_category_shipping_cost')}}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="col-12">
                        <table class="table table-bordered" width="100%" cellspacing="0"
                            style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            <thead>
                                <tr>
                                    <th scope="col">{{translate('sl#')}}</th>
                                    <th scope="col">{{translate('category_name')}}</th>
                                    <th scope="col">{{translate('cost_per_product')}}</th>
                                    <th scope="col">{{translate('multiply_with_QTY')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <form action="{{route('seller.business-settings.category-shipping-cost.store')}}" method="POST">
                                    @csrf
                                    @foreach ($all_category_shipping_cost as $key=>$item)
                                        <tr>
                                            <td>
                                                {{$key+1}}
                                            </td>
                                            <td>
                                                {{$item->category!=null?$item->category->name:translate('not_found')}}
                                            </td>
                                            <td>
                                                <input type="hidden" class="form-control" name="ids[]" value="{{$item->id}}">
                                                <input type="number" class="form-control" min="0" step="0.01" name="cost[]" value="{{\App\CPU\BackEndHelper::usd_to_currency($item->cost)}}">
                                            </td>
                                            <td>
                                                <label class="switch">
                                                    <input type="checkbox" name="multiplyQTY[]"
                                                        id="" value="{{$item->id}}" {{$item->multiply_qty == 1?'checked':''}}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4">
                                            <button type="submit" class="btn btn-primary ">{{translate('Update')}}</button>
                                        </td>
                                    </tr>
                                </form>
                            </tbody>

                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
            let shipping_type = '{{$shippingType}}';

            if(shipping_type==='category_wise')
            {
                $('#product_wise_note').hide();
                $('#order_wise_shipping').hide();
                $('#update_category_shipping_cost').show();

            }else if(shipping_type==='order_wise'){
                $('#product_wise_note').hide();
                $('#update_category_shipping_cost').hide();
                $('#order_wise_shipping').show();
            }else{

                $('#update_category_shipping_cost').hide();
                $('#order_wise_shipping').hide();
                $('#product_wise_note').show();
            }
        });
        $(document).on('change', '.status', function () {
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
                url: "{{route('seller.business-settings.shipping-method.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function () {
                    toastr.success('{{translate('order wise shipping method Status updated successfully')}}');
                }
            });
        });
        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{translate('Are you sure delete this ?')}}',
                text: "{{translate('You wont be able to revert this!')}}",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{translate('Yes, delete it!')}}'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('seller.business-settings.shipping-method.delete')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function () {
                            toastr.success('{{translate('Shipping Method deleted successfully')}}');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
    <script>
        function seller_shipping_type(val)
        {
            console.log("val");
            if(val==='category_wise')
            {
                $('#product_wise_note').hide();
                $('#order_wise_shipping').hide();
                $('#update_category_shipping_cost').show();
            }else if(val==='order_wise'){
                $('#product_wise_note').hide();
                $('#update_category_shipping_cost').hide();
                $('#order_wise_shipping').show();
            }else{
                $('#update_category_shipping_cost').hide();
                $('#order_wise_shipping').hide();
                $('#product_wise_note').show();
            }

            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{route('seller.business-settings.shipping-type.store')}}",
                    method: 'POST',
                    data: {
                        shippingType: val
                    },
                    success: function (data) {
                        toastr.success('{{translate('shipping_method_updated_successfully!!')}}');
                    }
                });
        }
    </script>
@endpush
