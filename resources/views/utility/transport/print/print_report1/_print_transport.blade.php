<div class="content" style="border: #0b0b0b 3px solid; padding:3px">
    <div style="text-align: center; padding: 3px">
        {{$header_text}}
        <br/>
        گزارش ارسال بار به تفکیک کد کالا
        <hr style="margin: 0px; padding: 0px"/>
    </div>
    <table style="width: 100%; border:none;">

        <tr>
            <td style="border:none;text-align: right">
                نام مشتری:
                {{$transport->customer_caption}}</td>

            <td style="border:none">شماره بار:
                {{$transport->getCode()}}</td>


            <td style="border:none">تاریخ:
                {{$transport->created_date_time()}}</td>

        </tr>
        <tr>
            <td colspan="3" style="border:none;text-align: right">
                تعداد بسته بندی حمل و نقل:
                {{$transport->items()->count()}}
            </td>
        </tr>


    </table>
    <br/>
    <br/>


    @include("utility.transport.dashboard._table_product_list")
    <br/>


</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
