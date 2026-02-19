<html>
<head>
    @include("pdf._head")
</head>
<body>

<div class="content">


    <table style=" display:inline">
        <tr>
            <td colspan="10">
                درخواست محصول از انبار
            </td>
        </tr>
        @if($include_header)
            <tr>
                <td colspan="2" style="text-align: right">
                    کد سفارش :
                    {{$order->code()}}

                </td>
                <td colspan="2">
                    کانال توزیع:
                    {{$order->customer->channelType->caption??""}}
                </td>
                <td colspan="4">
                    نام مرکز:
                    {{$order->customer->caption??""}}
                </td>
                <td>
                    کد مرکز:
                    {{$order->customer->code??""}}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    تاریخ درخواست :

                    {{$order->order_date()}}
                </td>
                <td colspan="3">
                    تعداد روز در انتظار :
                    {{$order->number_of_days_waiting()??""}}
                </td>
                <td colspan="3">
                    مجوز خروج :
                    {{$order->exit_date()??""}}
                </td>
                <td colspan="1">
                    اولویت سفارش :
                    {{$order->priority->caption??""}}
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    وزن کل:
                    {{$order->total_weight??""}}
                </td>
                <td colspan="4">
                    وزن بار ارسال نشده :
                    {{$order->total_weight_remaining??""}}
                </td>
                <td colspan="2">
                    نسبت وزنی ارسال شده :
                    {{($order->total_weight_sent_raito??"" ). "%"}}
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    وضعیت:
                    {{$order->status->caption??""}}
                </td>
                <td colspan="4">
                    وزن بخش از بار موجود در انبار :
                    {{$order->total_weight_in_warehouse??""}}
                </td>
                <td colspan="2">
                    نسبت وزنی موجود در انبار :
                    {{($order->total_weight_in_warehouse_raito??"" ). "%"}}
                </td>
            </tr>
        @endif
        <tr>
            <td>ردیف</td>
            <td>کد کالا</td>
            <td>عنوان کالا</td>
            <td>واحد</td>
            <td>تعداد در <br/>واحد اصلی</td>
            <td>مقدار</td>
            <td>مقدار تحویلی</td>
            <td>مقدار جمع آوری شده</td>
            <td>مقدار بارگیری</td>
            <td style="width: 200px">توضیحات</td>
        </tr>
        @php $i=1;@endphp
        @foreach ($order->orderList as $item)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$item->product->code}}</td>
                <td>{{$item->product->caption}}</td>
                <td>{{$item->product->unit->bach_caption}}</td>
                <td>{{$item->product->number_in_carton}}</td>
                <td>{{$item->carton}}</td>
                <td>{{$item->amount_sent}}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2" style="height: 100px">
                درخواست کننده
            </td>
            <td colspan="2">
                تایید کننده
            </td>
            <td colspan="3">
                تحویل دهنده
            </td>
            <td colspan="1">
                تحویل گیرنده
            </td>
        </tr>


    </table>


</div>
</body>
</html>
