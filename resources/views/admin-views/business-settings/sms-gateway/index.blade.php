@extends('layouts.back-end.app')

@push('css_or_js')

@endpush

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a></li>
            <li class="breadcrumb-item" aria-current="page">{{translate('SMS')}} {{translate('Gateway')}} </li>
        </ol>
    </nav>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h4 class="mb-0 text-black-50">{{translate('SMS')}} {{translate('Gateway')}}</h4>
    </div>

    <div class="row" style="padding-bottom: 20px">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body" style="padding: 20px">
                    <h5 class="text-center">{{translate('NEXMO')}} {{translate('SMS')}} </h5>
                    @php($config=\App\CPU\Helpers::get_business_settings('sms_nexmo'))
                    <form action="{{route('admin.business-settings.sms-gateway.update',['sms_nexmo'])}}"
                          method="post">
                        @csrf
                        @if(isset($config))
                            <div class="form-group mb-2">
                                <label class="control-label">{{translate('nexmo_gateway')}}</label>
                            </div>
                            <div class="form-group mb-2 mt-2">
                                <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                <label style="padding-left: 10px">{{translate('Active')}}</label>
                                <br>
                            </div>
                            <div class="form-group mb-2">
                                <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                <label style="padding-left: 10px">{{translate('Inactive')}}</label>
                                <br>
                            </div>
                            <div class="form-group mb-2">
                                <label style="padding-left: 10px">{{translate('api_key')}}</label><br>
                                <input type="text" class="form-control" name="nexmo_key" value="{{$config['nexmo_key']}}">
                            </div>
                            <div class="form-group mb-2">
                                <label style="padding-left: 10px">{{translate('api_secret')}}</label><br>
                                <input type="text" class="form-control" name="nexmo_secret"
                                       value="{{$config['nexmo_secret']}}">
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">{{translate('Save')}}</button>
                        @else
                            <button type="submit" class="btn btn-primary mb-2">{{translate('Configure')}}</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')

@endpush
