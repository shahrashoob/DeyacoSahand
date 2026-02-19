<!DOCTYPE html>
<html lang="en">
<head>
    @include("layouts._head")

    <style>
        body {
            font-family: IRANSans !important;
        }

        .mb-4, .my-4 {

        }
    </style>
</head>
<body>
<div class="auth-wrapper">

    <div class="auth-content subscribe" style="width:450px">
        @include("layouts._messages",["type"=>'public_1'])
        <form id="form1" method="post" action="{{route('login_sms')}}" autocomplete="false">
            @csrf
            <div class="card">
                <div class="row no-gutters">
{{--                    <div class="col-md-12" style="text-align: center">--}}
{{--                        <img src="{{asset("assets/images/login_logo.png")}}" alt="lock images" class="img-fluid"--}}
{{--                             style="width: 200px">--}}
{{--                    </div>--}}
                    <div class="col-md-12 col-lg-12">
                        <div class="card-body text-center">
                            <div class="row justify-content-center">
                                <div class="col-md-12">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12"
                         style="text-align: center;font-weight: bold; font-size: 16px; margin-top: -60px">
                        لطفا زبان خود را انتخاب نمایید.
                        <div style=" direction: ltr">
                            (Please choose your language)
                        </div>

                    </div>
                    <div class="col-md-6 center">
                        <a href="{{route("language","fa")}}" style="font-weight: bold;">
                            <img src="{{asset("assets/images/language/fa.svg")}}" alt="lock images" class="img-fluid"
                                 style="width: 100px">
                            <br>
                            فارسی</a>
                    </div>
                    <div class="col-md-6 center">
                        <a href="{{route("language","en")}}" style="font-weight: bold;">
                            <img src="{{asset("assets/images/language/en.svg")}}" alt="lock images" class="img-fluid"
                                 style="width:100px">
                            <br/>
                            English
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

@include("layouts._footer")
<script type="text/javascript">
    $('#form1').validate({
        rules: {

            mobile: {
                required: true,

            },
            melicode: {required: true},
            Captcha: {required: true},
            errorClass: 'help-block',
            errorElement: 'span',
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-success').addClass('has-error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error').addClass('has-success');
            }
        }
    });
</script>
</body>


</html>
