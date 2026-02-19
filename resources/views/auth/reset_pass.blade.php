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

    <div class="auth-content subscribe">
        @include("layouts._messages")
        <form id="form1" method="post" action="{{route('submit_reset_pass')}}" autocomplete="false">
            @csrf
            <div class="card">
                <div class="row no-gutters">
                    <div class="col-md-12">
                        <h4 style="text-align: center; font-weight: bold;
    margin-top: 50px;
    margin-bottom: 10px; " class="mb-4"> بازیابی کلمه عبور</h4>
                    </div>
                    <div class="col-md-12 col-lg-12">
                        <div class="card-body text-center">
                            <div class="row justify-content-center">
                                <div class="col-sm-6">

                                    <h5 class="mb-4" style="margin-bottom: 0 !important;">{{env("APP_COMPANY")}}</h5>

                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" autocomplete="new-password" name="mobile"
                                               placeholder=" شماره موبایل (بدون صفر)"  >
                                    </div>

                                    <div class="input-group mb-4">
                                        <input type="text" class="form-control" autocomplete="new-password"
                                               name="national_code"  placeholder="کد ملی" >
                                    </div>
                                    <div style="text-align: left;margin-top: -20px;margin-bottom: 15px ">
                                        <a style=" color: #0a6aa1!important;font-size:12px;font-weight: unset" href="{{route('login')}}" >ورود به سامانه</a>
                                    </div>
                                    <button class="btn btn-primary shadow-2 mb-4" style="width: 100%;padding: 5px">ارسال کد تایید</button>
<br/>
                                    <br/>

                                </div>

                            </div>
                        </div>
                    </div>
{{--                    <div--}}
{{--                        class="col-md-4 col-lg-6 d-none d-md-flex d-lg-flex  align-items-center justify-content-center">--}}
{{--                        <img src="{{asset("assets/images/login_logo.png")}}" alt="lock images" class="img-fluid">--}}
{{--                    </div>--}}
                    <div class="col-md-12"
                         style="text-align: center;font-weight: bold; font-size: 12px; margin-top: -30px">طراحی و تولید
                        شرکت صنعتی دیاکو مهن آریا


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

            national_code: {
                required: true
            },
            mobile: {required: true,maxlength:10},
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
