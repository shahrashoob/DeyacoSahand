<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($is_verify)
            پرداخت موفقیت‌آمیز
        @else
            تراکنش ناموفق
        @endif
    </title>

    <link rel="stylesheet" class="rtl-css" href="{{asset('assets/fonts/fontiran/fontiran.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/payment/payment.css')}}" >
</head>
<body>
<div class="container">
    <div class="success-icon">
        <img src="{{asset('assets/css/payment/'.($is_verify?"check_circle":"cancel").'.png')}}">
    </div>
    @if($is_verify)
        <h1>پرداخت موفقیت‌آمیز بود!</h1>
        <p>تراکنش شما با موفقیت انجام شد. از خرید شما سپاسگزاریم.</p>
        <ul class="listsu">
            <li style="text-align: right">کد رهگیری بانک : {{$client_payment->track_id??""}}</li>
            <li style="text-align: right">شناسه پرداخت :{{$client_payment->id??""}} </li>
            <li style="text-align: right">شناسه سفارش :{{$client_payment->order_id_in_deyaco??""}} </li>
        </ul>
        <br/>

        <button class="button_success" onclick="window.location.href='{{route("login")}}'">بازگشت به صفحه اصلی</button>


    @else
        <h3>تراکنش ناموفق</h3>
        <p>متأسفانه تراکنش شما انجام نشد. لطفاً مجدداً تلاش کنید یا با پشتیبانی تماس بگیرید.</p>
        <p>
            پرداخت شما بنا به دلایل زیر ناموفق بوده است.
        </p>
        <p>

          <b>  {{$message??""}}</b>
        </p>
        <button class="button_error" onclick="window.location.href='{{route("login")}}'">بازگشت به صفحه اصلی</button>

    @endif
</div>
</body>
</html>
