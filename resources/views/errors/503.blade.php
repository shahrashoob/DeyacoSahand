{{--@extends('errors::minimal')--}}

{{--@section('title', __('Service Unavailable'))--}}
{{--@section('code', '503')--}}
{{--@section('message', __('Service Unavailable'))--}}
<!DOCTYPE html>
<html lang="en">
<head>
    @include("layouts._head")

    <style>
        body {
            font-family: IRANSans !important;
        }

    </style>
</head>
<body>

<div class="auth-wrapper">

    <div class="auth-content subscribe">

        @include("layouts._messages",["type"=>'public_1'])

        <div class="alert alert-danger text-center">
            با عرض پوزش، در حال حاضر سایت ما در دسترس نمی‌باشد و امکان ارائه خدمات وب موقتاً متوقف شده است. لطفاً بعداً مراجعه کنید یا با مدیر سایت تماس بگیرید. با تشکر از صبر و شکیبایی شما.

        </div>
        <div class="col-md-12 col-lg-12 d-none d-md-flex d-lg-flex  align-items-center justify-content-center">
            <img src="{{asset("assets/images/500.png")}}" style="width: 100%;" alt="lock images" class="img-fluid">
        </div>
    </div>
</div>

@include("layouts._footer")
</body>
</html>
