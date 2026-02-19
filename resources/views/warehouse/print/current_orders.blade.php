<html>
<head>
    @include("pdf._head",["font_size"=>10])
</head>
<body>

<div class="content">




    <table style=" display:inline">
        <tr>
            <td>کد سفارش</td>
            <td>کانال توزیع</td>
            <td>نام مرکز</td>
            <td>کد مرکز</td>
            <td>تاریخ درخواست</td>
            <td>مجوز خروج</td>
            <td>تعداد روز در  <br/> اتنظار ارسال</td>
            <td>اولویت سفارش</td>
            <td>وزن کل Kg</td>
            <td>وزن بار ارسال نشده Kg</td>
            <td>نسبت وزنی ارسال شده</td>
            <td>وزن  بار موجود در انبار</td>
            <td>نسبت وزنی موجود در انبار</td>
            <td>وضعیت</td>
        </tr>

        @foreach ($list as $order)
            @php
                $order= $order->calculate();
            @endphp
            <tr>
                <td>{{$order->code()}}</td>
                <td>{{$order->customer->channelType->caption??""}}</td>
                <td>{{$order->customer->caption??""}}</td>
                <td>{{$order->customer->code??""}}</td>
                <td>{{$order->order_date()}}</td>
                <td>{{$order->exit_date()??""}}</td>
                <td>{{$order->number_of_days_waiting()??""}}</td>
                <td>{{$order->priority->caption??""}}</td>
                <td>{{$order->total_weight??""}}</td>
                <td>{{$order->total_weight_remaining??""}}</td>
                <td>{{($order->total_weight_sent_raito??"" ). "%"}}</td>
                <td>{{$order->total_weight_in_warehouse??""}}</td>
                <td>{{($order->total_weight_in_warehouse_raito??"" ). "%"}}</td>
                <td>{{$order->status->caption??""}}</td>
            </tr>
        @endforeach
    </table>
</div>
</body>
</html>
