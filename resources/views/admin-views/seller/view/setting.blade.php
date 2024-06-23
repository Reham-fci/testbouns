@extends('layouts.back-end.app')

@section('title',$seller->shop ? $seller->shop->name : translate("shop name not found"))

@push('css_or_js')
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

        input:checked + .slider {
            background-color: #377dff;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #377dff;
        }

        input:checked + .slider:before {
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


    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{route('admin.dashboard.index')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('Seller_details')}}</li>
            </ol>
        </nav>

        <!-- Page Heading -->
        <div class="flex-between d-sm-flex row align-items-center justify-content-between mb-2 mx-1">
            <div>
                <a href="{{route('admin.sellers.seller-list')}}" class="btn btn-primary mt-3 mb-3">{{translate('Back_to_seller_list')}}</a>
            </div>
            <div>
                @if ($seller->status=="pending")
                    <div class="mt-4 pr-2">
                        <div class="flex-start">
                            <div class="mx-1"><h4><i class="tio-shop-outlined"></i></h4></div>
                            <div><h4>{{translate('Seller request for open a shop')}}.</h4></div>
                        </div>
                        <div class="text-center">
                            <form class="d-inline-block" action="{{route('admin.sellers.updateStatus')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$seller->id}}">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-primary">{{translate('Approve')}}</button>
                            </form>
                            <form class="d-inline-block" action="{{route('admin.sellers.updateStatus')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$seller->id}}">
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-danger">{{translate('reject')}}</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- Page Header -->
        <div class="page-header">
            <div class="flex-between mx-1 row">
                <div>
                    <h1 class="page-header-title">{{ $seller->shop ? $seller->shop->name : "Shop Name : Update Please" }}</h1>
                </div>

            </div>
            <!-- Nav Scroller -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                <!-- Nav -->
                <ul class="nav nav-tabs page-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('admin.sellers.view',$seller->id) }}">{{translate('Shop')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link "
                           href="{{ route('admin.sellers.view',['id'=>$seller->id, 'tab'=>'order']) }}">{{translate('Order')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.sellers.view',['id'=>$seller->id, 'tab'=>'product']) }}">{{translate('Product')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active"
                           href="{{ route('admin.sellers.view',['id'=>$seller->id, 'tab'=>'setting']) }}">{{translate('Setting')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.sellers.view',['id'=>$seller->id, 'tab'=>'transaction']) }}">{{translate('Transaction')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.sellers.view',['id'=>$seller->id, 'tab'=>'review']) }}">{{translate('Review')}}</a>
                    </li>

                </ul>
                <!-- End Nav -->
            </div>
            <!-- End Nav Scroller -->
        </div>
        <!-- End Page Header -->

        <div class="row">
            <div class="col-md-6 mt-3">
                <form action="{{ url()->current() }}"
                      style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                      method="GET">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <label> {{translate('Sales Commission')}} : </label>
                            <label class="switch ml-3">
                                <input type="checkbox" name="commission_status"
                                       class="status"
                                       value="1" {{$seller['sales_commission_percentage']!=null?'checked':''}}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="card-body" style="overflow-x: scroll">
                            <small class="badge badge-soft-danger mb-3">
                                {{translate('If sales commission is disabled here, the system default commission will be applied')}}.
                            </small>
                            <div class="form-group">
                                <label>{{translate('Commission')}} ( % )</label>
                                <input type="number" value="{{$seller['sales_commission_percentage']}}"
                                       class="form-control" name="commission">
                            </div>
                            <button type="submit" class="btn btn-primary">{{translate('Update')}}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 mt-3">
                <form action="{{ url()->current() }}"
                      style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                      method="GET">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <label> {{translate('GST Number')}} : </label>
                            <label class="switch ml-3">
                                <input type="checkbox" name="gst_status"
                                       class="status"
                                       value="1" {{$seller['gst']!=null?'checked':''}}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="card-body" style="overflow-x: scroll">
                            <small class="badge badge-soft-danger mb-3">
                                {{translate('If GST number is disabled here, it will not show in invoice')}}.
                            </small>
                            <div class="form-group">
                                <label> {{translate('Number')}}  </label>
                                <input type="text" value="{{$seller['gst']}}"
                                       class="form-control" name="gst">
                            </div>
                            <button type="submit" class="btn btn-primary">{{translate('Update')}} </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 mt-2">
                <div class="card">
                    <div class="card-header">
                        <h5>{{translate('Seller POS')}}</h5>
                    </div>

                    <div class="card-body" style="padding: 20px">
                        <form action="{{ url()->current() }}"
                              method="GET">
                            @csrf
                            <label>{{translate('Seller POS permission on/off')}}</label>
                            <div class="form-check">
                                <input class="form-check-input" name="seller_pos" type="radio" value="1"
                                       id="seller_pos1" {{$seller['pos_status']==1?'checked':''}}>
                                <label class="form-check-label {{Session::get('direction') === "rtl" ? 'mr-3' : 'ml-3'}}" for="seller_pos1">
                                    {{translate('Turn on')}}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" name="seller_pos" type="radio" value="0"
                                       id="seller_pos2" {{$seller['pos_status']==0?'checked':''}}>
                                <label class="form-check-label {{Session::get('direction') === "rtl" ? 'mr-3' : 'ml-3'}}" for="seller_pos2">
                                    {{translate('Turn off')}}
                                </label>
                            </div>
                            <hr>
                            <button type="submit"
                                    class="btn btn-primary {{Session::get('direction') === "rtl" ? 'float-left mr-3' : 'float-right ml-3'}}">{{translate('Save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush
