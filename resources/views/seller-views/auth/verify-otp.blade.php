

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <title>{{translate('forgot_password')}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('public/assets/back-end')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/back-end')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('public/assets/back-end')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('public/assets/back-end')}}/css/toastr.css">

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
            width: 220px;
            min-width: 220px;
        }

        #divInner{
            left: 0;
            position: sticky;
        }

        #divOuter{
            width: 190px;
            overflow: hidden;
        }
    </style>
</head>

<body>
<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main">
    <div class="position-fixed top-0 right-0 left-0 bg-img-hero"
         style="height: 32rem; background-image: url({{asset('public/assets/admin')}}/svg/components/abstract-bg-4.svg);">
        <!-- SVG Bottom Shape -->
        <figure class="position-absolute right-0 bottom-0 left-0">
            <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1921 273">
                <polygon fill="#fff" points="0,273 1921,273 1921,0 "/>
            </svg>
        </figure>
        <!-- End SVG Bottom Shape -->
    </div>

    <!-- Content -->
    <div class="container py-5 py-sm-7">
        @php($e_commerce_logo=\App\Model\BusinessSetting::where(['type'=>'company_web_logo'])->first()->value)
        <a class="d-flex justify-content-center mb-5" href="javascript:">
            <img class="z-index-2"  src="{{asset("storage/app/public/company/".$e_commerce_logo)}}" alt="Logo"
                 onerror="this.src='{{asset('public/assets/back-end/img/400x400/img2.jpg')}}'"
                 style="width: 8rem;">
        </a>

        <div class="container py-4 py-lg-5 my-4">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <h2 class="h3 mb-4">{{translate('provide_your_otp_and_proceed')}}?</h2>
                    <div class="card py-2 mt-4">
                        <form class="card-body needs-validation" action="{{route('seller.auth.otp-verification')}}"
                              method="post">
                            @csrf
                            <div class="form-group">
                                <label>{{translate('Enter your OTP')}}</label>
                                <div id="divOuter">
                                    <div id="divInner">
                                        <input id="partitioned" class="form-control" name="otp" type="text" maxlength="4" />
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit">{{translate('proceed')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- End Content -->
</main>
<!-- ========== END MAIN CONTENT ========== -->


<!-- JS Implementing Plugins -->
<script src="{{asset('public/assets/back-end')}}/js/vendor.min.js"></script>

<!-- JS Front -->
<script src="{{asset('public/assets/back-end')}}/js/theme.min.js"></script>
<script src="{{asset('public/assets/back-end')}}/js/toastr.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<!-- JS Plugins Init. -->
<script>
        var obj = document.getElementById('partitioned');
        obj.addEventListener('keydown', stopCarret);
        obj.addEventListener('keyup', stopCarret);

        function stopCarret() {
            if (obj.value.length > 3){
                setCaretPosition(obj, 3);
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
    </script>
</body>
</html>

