@extends('layouts.front-end.app')

@section('title', translate('Reset Password'))

@push('css_or_js')
    <style>
        /* .text-primary{
            color: <?=$web_config['primary_color']?> !important;
        } */
    </style>
@endpush

@section('content')
    <div class="container py-4 py-lg-5 my-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <h2 class="h3 mb-4">{{translate('Reset your password')}}</h2>
                <p class="font-size-md">{{translate('Change your password in two easy steps. This helps to keep your new password secure')}}.</p>
                <ol class="list-unstyled font-size-md">
                    <li><span class="text-primary mr-2">{{translate('1')}}.</span>{{translate('New Password')}}.</li>
                    <li><span class="text-primary mr-2">{{translate('2')}}.</span>{{translate('Confirm Password')}}.</li>
                </ol>
                <div class="card py-2 mt-4">
                    <form class="card-body" id="reset_password" method="POST"
                        action="{{route('customer.auth.change-password2')}}">
                        @csrf
                        <div class="form-group" style="display: none">
                            <input type="text" name="reset_token" value="{{$id}}" required>
                        </div>

                        <div class="form-group">
                                <label for="si-password">{{translate('New')}}{{translate('password')}}</label>
                                <div class="password-toggle">
                                    <input class="form-control" name="password" type="password" id="si-password"
                                           required>
                                    <label class="password-toggle-btn">
                                        <input class="custom-control-input" type="checkbox"><i
                                            class="czi-eye password-toggle-indicator"></i><span
                                            class="sr-only">{{translate('Show')}} {{translate('password')}} </span>
                                    </label>
                                    <div class="invalid-feedback">{{translate('Please provide valid password')}}.</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="si-password">{{translate('confirm_password')}}</label>
                                <div class="password-toggle">
                                    <input class="form-control" name="confirm_password" type="password" id="si-password"
                                           required>
                                    <label class="password-toggle-btn">
                                        <input class="custom-control-input" type="checkbox"><i
                                            class="czi-eye password-toggle-indicator"></i><span
                                            class="sr-only">{{translate('Show')}} {{translate('password')}} </span>
                                    </label>
                                    <div class="invalid-feedback">{{translate('Please provide valid password')}}.</div>
                                </div>
                            </div>

                        <button class="btn btn-primary" type="submit">{{translate('Reset password')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush
