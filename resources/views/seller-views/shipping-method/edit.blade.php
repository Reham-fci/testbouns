@extends('layouts.back-end.app-seller')
@section('title', translate('Edit Shipping'))
@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-black-50">{{translate('shipping_method')}} {{translate('update')}}</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-capitalize">
                    {{translate('shipping_method_update')}}
                </div>
                <div class="card-body">
                    <form action="{{route('seller.business-settings.shipping-method.update',[$method['id']])}}" method="post"
                          style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        @csrf
                        @method('put')
                        <div class="form-group">
                            <div class="row ">
                                <div class="col-md-12">
                                    <label for="title">{{translate('title')}}</label>
                                    <input type="text" name="title" value="{{$method['title']}}" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row ">
                                <div class="col-md-12">
                                    <label for="duration">{{translate('duration')}}</label>
                                    <input type="text" name="duration" value="{{$method['duration']}}" class="form-control" placeholder="{{translate('Ex')}} : 4-6 {{translate('days')}}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row ">
                                <div class="col-md-12">
                                    <label for="cost">{{translate('cost')}}</label>
                                    <input type="text" min="0" max="1000000" name="cost" value="{{\App\CPU\BackEndHelper::usd_to_currency($method['cost'])}}" class="form-control" placeholder="{{translate('Ex')}} : 10 $">
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary float-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">{{translate('Update')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')

@endpush
