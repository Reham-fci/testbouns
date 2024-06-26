@extends('layouts.front-end.app')

@section('title', translate('OTP_verification'))

@push('css_or_js')
    <style>
        #partitioned {
            padding-left: 2px;
            letter-spacing: 42px;
            border: 0;
            background-image: linear-gradient(to left, black 70%, rgba(255, 255, 255, 0) 0%);
            background-position: bottom;
            background-size: 50px 1px;
            background-repeat: repeat-x;
            background-position-x: 35px;
            width: 100%;
            min-width: 220px;
        }

        #divInner{
            left: 0;
            position: sticky;
        }

        #divOuter{
            width: 100%;
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4 py-lg-5 my-4">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6">
                <h2 class="h3 mb-4">{{translate('provide_your_otp_and_proceed')}}?</h2>
                <div class="card py-2 mt-4">
                    <form class="card-body needs-validation" action="{{route('customer.auth.otp-verification2')}}"
                          method="post">
                        @csrf
                        <div class="form-group">
                            <label>{{translate('Enter your OTP')}}</label>
                            <div id="divOuter">
                                <div id="divInner">
                                    <input type="hidden" name="sessionInfo" value="{{$sessionInfo}}">
                                    <input type="hidden" name="phone" value="{{$identity}}">
                                    <input type="hidden" name="recaptchaToken" value="{{$recaptchaToken}}">
                                    <input id="partitioned" class="form-control" name="otp" type="text" maxlength="6" />
                                </div>
                                <a href="#" id="resend">Resend Code Again</a>
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">{{translate('proceed')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        var obj = document.getElementById('partitioned');
        obj.addEventListener('keydown', stopCarret);
        obj.addEventListener('keyup', stopCarret);

        function stopCarret() {
            if (obj.value.length > 5){
                setCaretPosition(obj, 5);
            }
        }

        function setCaretPosition(elem, caretPos) {
            if(elem != null) {
                if(elem.createTextRange) {
                    var range = elem.createTextRange();
                    range.move('character', caretPos);
                    range.select();
                }
                else {
                    if(elem.selectionStart) {
                        elem.focus();
                        elem.setSelectionRange(caretPos, caretPos);
                    }
                    else
                        elem.focus();
                }
            }
        }
        $('#resend').on('click',function(){
            $.ajax({
                type: "post",
                url: "{{route('customer.auth.reset_password-ajax')}}",
                data: {
                    _token: "{{ csrf_token() }}",
                    identity:"{{$identity}}",
                    recaptchaToken:"{{$recaptchaToken}}"
                },
                dataType: "json",
                success: function (response) {
                    // $('#sessionInfo').val(response.sessionInfo);
                }
            });
        })
    </script>
@endpush
