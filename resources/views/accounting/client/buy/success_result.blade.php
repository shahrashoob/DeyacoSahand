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
        @include("layouts._messages",["type"=>'public_1'])
        <form id="form1" method="post" action="{{route('login')}}" autocomplete="false">
            @csrf
            <div class="card">
                <div class="row no-gutters">
                    <div class="col-md-12">
                        <h3 style="text-align: center; font-weight: bold;
    margin-top: 50px;
    margin-bottom: 10px; " class="mb-4">  {{$setting["software_name"]->string_value??""}}</h3>
                    </div>
                    <div class="col-md-12 center" >
                        <div class="alert alert-success" style="margin: 15px">
                            <h3>تراکنش موفق</h3>
                        </div>
                        <div class="table-responsive center" style="margin: 15px">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>شرح خدمات</th>
                                    <th>قیمت</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>شارژ حساب</td>
                                    <td> {{$finance_payment->amount}} ریال </td>
                                </tr>
                                <tr style="font-weight: bold">
                                    <td>مبلغ کل</td>
                                    <td> {{$finance_payment->amount}} ریال </td>
                                </tr>
                                <tr style="font-weight: bold" class="">
                                    <td>شناسه پرداخت</td>
                                    <td> {{$finance_payment->driver_order_id}}  </td>
                                </tr>
                                </tbody>
                            </table>

                            <a href="{{route("dashboard")}}" class="btn btn-primary shadow-2 mb-4">بازگشت به پنل</a>

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
