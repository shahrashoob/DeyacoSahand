<html>
<head>
    @include("pdf._head",["font_size"=>20])
</head>
<body>

<div class="content">
    <table style=" display:inline">
        <tr>
            <td colspan="4" style="font-size: 28px; height:40px" >
                    برگ مشخصات پالت
            </td>
        </tr>
        <tr>
            <td colspan="4" style="font-size: 48px;font-weight:bold; height:220px">
            {{$production->product->caption}}
            </td>
        </tr>
        <tr>
            <td>تاریخ:</td>
            <td style="font-weight:bold;">
            {{$production->get_create_date()}}
            </td>


            <td>کد کالا:</td>
            <td style="font-weight:bold;">
            {{$production->product->code}}
            </td>
        </tr>
        <tr>

            <td>کد سفارش:</td>
            <td style="font-weight:bold; height:35px">
                @if(isset($production->order))
                {{ $production->order->code()}}
                @endif
            </td>


            <td>نام مشتری:</td>
            <td style="font-weight:bold;">
            {{$production->order->customer->caption??""}}
            </td>
        </tr>
        <tr>

            <td colspan="2" style="font-weight:bold;font-size: 25px; height:90px">

                {{$production->serial()}}
            </td>

            <td>تعداد در پالت:</td>
            <td style="font-weight:bold;">

            </td>
        </tr>
    </table>

</div>

</body>
</html>
