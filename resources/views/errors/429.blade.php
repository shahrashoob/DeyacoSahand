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
        <div class="alert alert-danger">
            429: تعداد درخواست های در یک دقیقه بیش از حد مجاز است، لطفا پس از 10 دقیقه دوباره تلاش کنید.
        </div>

    </div>
</div>

@include("layouts._footer")

</body>


</html>
