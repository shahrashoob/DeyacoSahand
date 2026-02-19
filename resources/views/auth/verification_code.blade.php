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
        @include("layouts._messages")
        <form id="form1" method="post" action="{{route('reset_pass_confirm_verification_code')}}" autocomplete="false">
            @csrf
            <div class="card">
                <div class="row no-gutters">
                    <div class="col-md-12" style="text-align: center">
                        <img src="{{asset("assets/images/login_logo.png")}}" alt="lock images" class="img-fluid"
                             style="width: 200px">
                    </div>
                    <div class="col-md-12 col-lg-12">
                        <div class="card-body text-center">
                            <div class="row justify-content-center">
                                <div class="col-sm-10">


                                    <div style="text-align: {{__("text-align")}}">
                                        <b style="font-size: 16px">{{__("please enter your code")}}</b>
                                        <br/>
                                        <br/>

                                    </div>
                                    <div class="form-group">
                                        <input name="token" id="token" value="" type="number" minlength="5"
                                               maxlength="5" class="form-control valid">

                                    </div>
                                    <button class="btn btn-primary shadow-2 mb-4" style="width: 100%">{{__("confirm code")}}</button>




                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12"
                         style="text-align: center;font-weight: bold; font-size: 12px; margin-top: -30px">

                        {{__("design by deyaco")}}

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
