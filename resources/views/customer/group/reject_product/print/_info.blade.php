<table style="border: none">
    <tr>
        <td style="border: none; text-align: center">
            <div style="font-size: .01px; position: fixed; left:0 ">
                {{$qr}}
            </div>
        </td>
        <td style="border: none; text-align: right;padding-right: 5px; font-size: 14px">

            شماره فرم مرجوعی: {{$reject_product_form->code}}
                <br/>
                شماره برگ خروج: {{$reject_product_form->exit_form->getCode()}}
                <br/>
                شماره سفارش: {{$reject_product_form->order?$reject_product_form->order->code():""}}

        </td>
        <td style="border: none; text-align: right;padding-right: 5px; font-size: 14px">
            تاریخ: {{$reject_product_form->get_create_date_and_time()}}
            <br/>
            تعداد بسته بندی {{$reject_product_form->items()->count()}} عدد
            <br/>

        </td>
    </tr>
</table>
