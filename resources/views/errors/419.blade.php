<!DOCTYPE html>
<html lang="en">
<head>
    @include("layouts._head")

    <style>
        body{
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
        <div class="alert alert-danger">
          419: نشست شما به پایان رسیده، لطفا دوباره وارد شوید
        </div>
        <form id="form1" method="post" action="{{route('login')}}">
            @csrf
            <div class="card">
                <div class="row no-gutters">
                    <div class="col-md-12">

                    </div>
                    <div class="col-md-8 col-lg-6">
                        <div class="card-body text-center">
                            <div class="row justify-content-center">
                                <div class="col-sm-10">

                                    <h5 class="mb-4" style="margin-bottom: 0 !important;">{{env("APP_COMPANY")}}</h5>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="email" placeholder="نام کاربری">
                                    </div>
                                    <div class="input-group mb-4">
                                        <input type="password" class="form-control" name="password" placeholder="کد واژه">
                                    </div>
                                    <button class="btn btn-primary shadow-2 mb-4">ورود</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-6 d-none d-md-flex d-lg-flex  align-items-center justify-content-center">
                        <img src="{{asset("assets/images/login_logo.png")}}" alt="lock images" class="img-fluid">
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

            password: {
                required: true
            },
            melicode: { required: true },
            Captcha:{ required: true},
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
