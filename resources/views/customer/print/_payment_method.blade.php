@php $k=0; @endphp
@foreach($order->order_payment_method as $item)
    @if($item->amount <=0)
        @continue
    @endif
    <br/>
    {{++$k}}
    @switch($item->payment_method_type_id)
        @case(10)
            تایید نهایی این سفارش منوط به پرداخت حداقل
            {{number_format($item->amount)}}
            ریال از مبلغ کل پیش فاکتور به‌عنوان پیش‌پرداخت می‌باشد.
            @break

        @case(20)
            پس از ثبت سفارش، جهت آغاز مراحل آماده‌سازی، پرداخت حداقل
            {{number_format($item->amount)}}
            ریال
            از مبلغ کل پیش فاکتور به‌عنوان پیش‌پرداخت الزامی است.
            @break

        @case(25)

            پس از ثبت سفارش، جهت آغاز مراحل آماده‌سازی، پرداخت حداقل
            {{number_format($item->amount)}}
            ریال
            از مبلغ کل پیش فاکتور به صورت اعتباری با سررسید حداکثر
            {{$item->check_delivery_days}}
            روزه به‌عنوان پیش‌پرداخت الزامی است.
            @break

        @case(30)
            تحویل و خروج کالا از انبار، منوط به پرداخت حداقل
            {{number_format($item->amount)}}
            ریال
            از مبلغ کل پیش فاکتور به‌صورت نقدی قبل از ارسال بار می‌باشد.
            @break

        @case(40)
            می بایست
            حداقل
            {{number_format($item->amount)}}
            ریال از مبلغ کل پیش
            فاکتور به‌صورت نقدی  پس از خروج بار از انبار پرداخت ‌گردد
            .
            @break

        @case(50)
            تحویل و خروج کالا از انبار، منوط به پرداخت حداقل
            {{number_format($item->amount)}}
            ریال
            از مبلغ کل پیش فاکتور به‌صورت اعتباری با سررسید حداکثر
            {{$item->check_delivery_days}} روزه
            قبل از ارسال بار می‌باشد.
            @break

        @case(60)

            میبایست
            حداقل
            {{number_format($item->amount)}}
            ریال از مبلغ کل پیش
            فاکتور به‌صورت اعتباری و با سررسید حداکثر
            {{$item->check_delivery_days}} روزه
             پس از خروج بار از انبار پرداخت گردد
            .
            @break
    @endswitch


@endforeach

@if($order->customer->cash_off_percent > 0)

    <br/>
    {{++$k}}
    در صورت پرداخت نقدی از {{$order->customer->cash_off_percent}} درصد تخفیف برخوردار خواهید
    شد.

@endif