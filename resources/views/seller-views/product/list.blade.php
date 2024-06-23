@extends('layouts.back-end.app-seller')

@section('title',translate('Product List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('seller.dashboard.index')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('Products')}}</li>

            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row flex-between justify-content-between align-items-center flex-grow-1">
                            <div class="col-12 mb-1 col-md-4">
                                <h5>{{ translate('Product')}} {{ translate('Table')}} ({{ $products->total() }})</h5>

                            </div>


                            <div class="col-12 mb-1 col-md-5">
                                <form action="{{ url()->current() }}" method="GET">
                                    <!-- Search -->
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{translate('Search by Product Name')}}" aria-label="Search orders" value="{{ $search }}" required>
                                        <button type="submit" class="btn btn-primary">{{translate('search')}}</button>
                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>
                            <div class="col-12 col-md-3">
                                <a href="{{route('seller.product.add-new')}}" class="btn btn-primary float-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                                    <i class="tio-add-circle"></i>
                                    <span class="text">{{translate('Add new product')}}</span>
                                </a>
                            </div>
                        </div>

                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table id="datatable"
                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                   class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                   style="width: 100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{translate('SL#')}}</th>
                                    <th>{{translate('Product Name')}}</th>
                                    <th>{{translate('purchase_price')}}</th>
                                    <th>{{translate('selling_price')}}</th>
                                    <th>{{translate('verify_status')}}</th>
                                    <th>{{translate('Active')}} {{translate('status')}}</th>
                                    <th style="width: 5px" class="text-center">{{translate('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $k=>$p)
                                    <tr>
                                        <th scope="row">{{$products->firstitem()+ $k}}</th>
                                        <td><a href="{{route('seller.product.view',[$p['id']])}}">
                                                {{\Illuminate\Support\Str::limit($p['name'],40)}}
                                            </a></td>
                                        <td>
                                            {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($p['purchase_price']))}}
                                        </td>
                                        <td>
                                            {{ \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($p['unit_price']))}}
                                        </td>
                                        <td>
                                            @if($p->request_status == 0)
                                                <label class="badge badge-warning">{{translate('New Request')}}</label>
                                            @elseif($p->request_status == 1)
                                                <label class="badge badge-success">{{translate('Approved')}}</label>
                                            @elseif($p->request_status == 2)
                                                <label class="badge badge-danger">{{translate('Denied')}}</label>
                                            @endif
                                        </td>
                                        <td>
                                            <label class="switch">
                                                <input type="checkbox" class="status"
                                                       id="{{$p['id']}}" {{$p->status == 1?'checked':''}}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <a class="btn btn-info btn-sm"
                                                title="{{translate('view')}}"
                                                href="{{route('seller.product.view',[$p['id']])}}">
                                                <i class="tio-visible"></i>
                                            </a>
                                            <a  class="btn btn-primary btn-sm"
                                                title="{{translate('Edit')}}"
                                                href="{{route('seller.product.edit',[$p['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a  class="btn btn-danger btn-sm" href="javascript:"
                                                title="{{translate('Delete')}}"
                                                onclick="form_alert('product-{{$p['id']}}','{{translate("Want to delete this item")}} ?')">
                                               <i class="tio-add-to-trash"></i>
                                            </a>
                                            <form action="{{route('seller.product.delete',[$p['id']])}}"
                                                  method="post" id="product-{{$p['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Footer -->
                     <div class="card-footer">
                        {{$products->links()}}
                    </div>
                    @if(count($products)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{translate('No data to show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
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
                url: "{{route('seller.product.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function (data) {
                    if(data.success == true) {
                        toastr.success('{{translate('Status updated successfully')}}');
                    }
                    else if(data.success == false) {
                        toastr.error('{{translate('Status updated failed. Product must be approved')}}');
                    }
                }
            });
        });
    </script>
@endpush
