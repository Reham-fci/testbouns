@extends('layouts.front-end.app')

@section('title', translate('Register'))

@push('css_or_js')
    <style>
        @media (max-width: 500px) {
            #sign_in {
                margin-top: -23% !important;
            }

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
    <div class="container py-4 py-lg-5 my-4"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 box-shadow">
                    <div class="card-body">
                        <h2 class="h4 mb-1">{{translate('no_account')}}</h2>
                        <p class="font-size-sm text-muted mb-4">{{translate('register_control_your_order')}}
                            .</p>
                        <form class="needs-validation_" action="{{route('customer.auth.sign-up')}}"
                              method="post" id="sign-up-form">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-fn">{{translate('first_name')}}</label>
                                        <input class="form-control" value="{{old('f_name')}}" type="text" name="f_name"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                               required>
                                        <div class="invalid-feedback">{{translate('Please enter your first name')}}!</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-ln">{{translate('last_name')}}</label>
                                        <input class="form-control" type="text" value="{{old('l_name')}}" name="l_name"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <div class="invalid-feedback">{{translate('Please enter your last name')}}!</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-email">{{translate('email_address')}}</label>
                                        <input class="form-control" type="email" value="{{old('email')}}"  name="email"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <div class="invalid-feedback">{{translate('Please enter valid email address')}}!</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-phone">{{translate('phone_number')}}
                                            <small class="text-primary">( * {{translate('country_code_is_must')}} {{translate('like_for_BD_880')}} )</small></label>
                                        <input class="form-control" type="number"  value="{{old('phone')}}"  name="phone"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                               required>
                                        <div class="invalid-feedback">{{translate('Please enter your phone number')}}!</div>
                                    </div>
                                </div>


                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{translate('password')}}</label>
                                        <div class="password-toggle">
                                            <input class="form-control" name="password" type="password" id="si-password"
                                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                   placeholder="{{translate('minimum_8_characters_long')}}"
                                                   required>
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input" type="checkbox"><i
                                                    class="czi-eye password-toggle-indicator"></i><span
                                                    class="sr-only">{{translate('Show')}} {{translate('password')}} </span>
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{translate('confirm_password')}}</label>
                                        <div class="password-toggle">
                                            <input class="form-control" name="con_password" type="password"
                                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                   placeholder="{{translate('minimum_8_characters_long')}}"
                                                   id="si-password"
                                                   required>
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input" type="checkbox"
                                                       style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"><i
                                                    class="czi-eye password-toggle-indicator"></i><span
                                                    class="sr-only">{{translate('Show')}} {{translate('password')}} </span>
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                {{-- here --}}
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{translate('city')}}</label>
                                        <div class="password-toggle">


                                            <select class="form-select form-control" name="city" type="city" id="si-city"
                                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                required>
                                                <option selected></option>
                                                @foreach ($governorates as $governorate)
                                                    <option value="{{$governorate->id}}">{{$governorate->governorate_name_ar}}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{translate('area')}}</label>
                                        <div class="password-toggle">


                                            <select class="form-select form-control" name="area" type="country" id="si-area"
                                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                required>
                                                <option selected></option>
                                                @foreach ($cities as $city)
                                                    <option class="city-non-active" data-parent="{{$city->governorate_id}}" value="{{$city->id}}">{{$city->city_name_ar}}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{translate('Type')}}</label>
                                        <div class="password-toggle">


                                            <select class="form-select form-control" name="type" type="type" id="si-Type"
                                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                required>
                                                <option selected></option>
                                                @foreach ($customertypes as $customertype)
                                                    <option value="{{$customertype->id}}">{{$customertype->ar_name}}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{translate('getFrom')}}</label>
                                        <div class="password-toggle">


                                            <select class="form-select form-control" name="getFrom" type="getFrom" id="si-Type"
                                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                >
                                                <option selected></option>
                                                <option value="type_facebook">
                                                    {{translate('type_facebook')}}
                                                </option>
                                                <option value="type_representative">
                                                    {{translate('type_representative')}}
                                                </option>
                                                <option value="type_nomination">
                                                    {{translate('type_nomination')}}
                                                </option>
                                                <option value="type_call">
                                                    {{translate('type_call')}}
                                                </option>
                                                <option value="type_linkdin">
                                                    {{translate('type_linkdin')}}
                                                </option>
                                                <option value="type_Google">
                                                    {{translate('type_Google')}}
                                                </option>
                                                <option value="type_other">
                                                    {{translate('type_other')}}
                                                </option>
                                            </select>

                                        </div>
                                    </div>

                                </div>
                                {{-- here --}}


                            </div>
                            <div class="form-group d-flex flex-wrap justify-content-between">

                                <div class="form-group mb-1">
                                    <strong>
                                        <input type="checkbox" class="mr-1"
                                               name="remember" id="inputCheckd">
                                    </strong>
                                    <label class="" for="remember">{{translate('i_agree_to_Your_terms')}}<a
                                            class="font-size-sm" target="_blank" href="{{route('terms')}}">
                                            {{translate('terms_and_condition')}}
                                        </a></label>
                                </div>

                            </div>
                            <div class="flex-between row" style="direction: {{ Session::get('direction') }}">
                                <div class="mx-1">
                                    <div class="text-right">
                                        <button class="btn btn-primary" id="sign-up" type="submit" disabled>
                                            <i class="czi-user {{Session::get('direction') === "rtl" ? 'ml-2 mr-n1' : 'mr-2 ml-n1'}}"></i>
                                            {{translate('sing_up')}}
                                        </button>
                                    </div>
                                </div>
                                <div class="mx-1">
                                    <a class="btn btn-outline-primary" href="{{route('customer.auth.login')}}">
                                        <i class="fa fa-sign-in"></i> {{translate('sing_in')}}
                                    </a>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="row">
                                        @foreach (\App\CPU\Helpers::get_business_settings('social_login') as $socialLoginService)
                                            @if (isset($socialLoginService) && $socialLoginService['status']==true)
                                                <div class="col-sm-6 text-center mt-1">
                                                    <a class="btn btn-outline-primary"
                                                       href="{{route('customer.auth.service-login', $socialLoginService['login_medium'])}}"
                                                       style="width: 100%">
                                                        <i class="czi-{{ $socialLoginService['login_medium'] }} {{Session::get('direction') === "rtl" ? 'ml-2 mr-n1' : 'mr-2 ml-n1'}}"></i>
                                                        {{translate('sing_up_with_'.$socialLoginService['login_medium'])}}
                                                    </a>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $('#inputCheckd').change(function () {
            // console.log('jell');
            if ($(this).is(':checked')) {
                $('#sign-up').removeAttr('disabled');
            } else {
                $('#sign-up').attr('disabled', 'disabled');
            }

        });

        $('#si-city').on('change',function(){
            var city = $('#si-city option:selected').val();
            $('#si-area option').removeClass('city-active');
            $('#si-area option[ data-parent="'+city+'"]').addClass('city-active');
        })


    </script>
@endpush
