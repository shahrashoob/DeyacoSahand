<!DOCTYPE html>
<html lang="en">
<head>
    @include("layouts._head")

</head>
<body>

<div class="auth-wrapper">

    <div class="auth-content subscribe">

        @include("layouts._messages",["type"=>'public_1'])
        <body class="loading">


        <div style="font-size: 20px; font-weight: bold; color: #0b0b0b; font-family: IRANSans !important">
            کاربر عزیز به دلایل الحاقی امکان انجام درخواست شما وجود ندارد، لطفا به قید فوریت با پشتیبانی سازمان دیجیتال
            دیاکو تماس بگیرید.
            <br/>
            <br/>
            <div style="text-align: center"><a style=" color: #2681D4" href="{{route("dashboard")}}">
                    <i class=" fa fa-home"></i>
                    بازگشت به صفحه
                    اصلی</a></div>
            <br/>
            <br/>
        </div>


        @include("layouts._footer")
        </body>
</html>
<script>$(function () {
        setTimeout(function () {
            $('body').removeClass('loading');
        }, 1000);
    });
</script>