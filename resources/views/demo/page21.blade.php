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
        input:invalid {
            border: red solid 3px;
        }
        input{
            font-weight: bold !important;
            font-size: 30px !important;
            letter-spacing: 20px;
        }
    </style>
</head>
<body>
<div class="auth-wrapper">

    <div class="auth-content subscribe">
        @include("layouts._messages",["type"=>'public_1'])
        <form id="form1" method="get" action="./page22" autocomplete="false">
            @csrf
            <div class="card">
                <div class="row no-gutters">
                    <div class="col-md-12">
{{--                        <h6 style="text-align: center; font-weight: bold;--}}
{{--    margin-top: 50px;--}}
{{--    margin-bottom: 10px; " class="mb-4">  {{$setting["software_name"]->string_value??""}}</h6>--}}

                    </div>
                    <div class="col-md-12 col-lg-12">
                        <div class="card-body text-center">
                            <div class="row justify-content-center">
                                <div class="col-sm-10">
                                    <h4 style="text-align: center; font-weight: bold;

    margin-bottom: 10px; " class="mb-4">
                                        ماژول داف کردن
                                    </h4>
                                    <h4 > شماره
                                        <span style="color: #481f5c; font-size: 30px">دوک</span>
                                        را وارد کنید </h4>

                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control"  autocomplete="new-password" autofocus name="email" pattern="[0-9]{5}" placeholder="_ _ _ _ _">
                                    </div>

                                    <button class="btn btn-lg btn-primary " style="width: 160px"> ثبت </button>
                                    <a class="btn btn-lg btn-outline-dark " href="{{route("dashboard")}} "  >بازگشت</a>

                                </div>
                            </div>
                        </div>
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
