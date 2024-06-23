@extends('layouts.front-end.app')

@section('title', translate('Verify'))

@push('css_or_js')
    <style>
        @media(max-width:500px){
            #sign_in{
                margin-top: -23% !important;
            }

        }
    </style>
@endpush

@section('content')
    <div class="container py-4 py-lg-5 my-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 box-shadow">
                    <div class="card-body">
                        <div class="text-center">
                            <h2 class="h4 mb-1">{{translate('one_step_ahead')}}</h2>
                            <p class="font-size-sm text-muted mb-4">{{translate('verify_information_to_continue')}}.</p>
                        </div>
                        <form class="needs-validation_" id="sign-up-form" action="{{ route('customer.auth.verify') }}"
                              method="post">
                            @csrf
                            <div class="col-sm-12">
                                @php($email_verify_status = \App\CPU\Helpers::get_business_settings('email_verification'))
                                @php($phone_verify_status = \App\CPU\Helpers::get_business_settings('phone_verification'))
                                <div class="form-group">
                                    @if(\App\CPU\Helpers::get_business_settings('email_verification'))
                                        <label for="reg-phone" class="text-primary">
                                            *
                                            {{translate('please') }}
                                            {{translate('provide') }}
                                            {{translate('verification') }}
                                            {{translate('token') }}
                                            {{translate('sent_in_your_email') }}
                                        </label>
                                    @elseif(\App\CPU\Helpers::get_business_settings('phone_verification'))
                                        <label for="reg-phone" class="text-primary">
                                            *
                                            {{translate('please') }}
                                            {{translate('provide') }}
                                            {{translate('OTP') }}
                                            {{translate('sent_in_your_phone') }}
                                        </label>
                                    @else
                                        <label for="reg-phone" class="text-primary">* {{translate('verification_code') }} / {{ translate('OTP')}}</label>
                                    @endif
                                    <input class="form-control" type="text" name="token" required>
                                </div>
                            </div>
                            <input type="hidden" value="{{$user->id}}" name="id">
                            <button type="submit" class="btn btn-outline-primary">{{translate('verify')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
@endpush
