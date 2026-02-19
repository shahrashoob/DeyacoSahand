<table style="border: none">
    <tr>
        <td style="border: none; text-align: center">
            <div style="font-size: .01px; position: fixed; left:0 ">
                @if(
                    (isset($product_request_form->order) && $product_request_form->order->selling_type_id>=1 )||
                    !isset($product_request_form->order)
                    )
                    {{$qr}}
                @else
                    <div style="min-width: 75px;min-height: 75px"></div>
                @endif
            </div>
        </td>
        <td style="border: none; text-align: right;padding-right: 5px; font-size: 14px">

            شماره برگ خروج از انبار: {{$form->code}}
            @if(
                    (isset($product_request_form->order) && $product_request_form->order->selling_type_id==1 )||
                    !isset($product_request_form->order)
                    )
                <br/>
                شماره فرم درخواست کالا: {{$form->getAllProductRequestFormCodes()}}
                <br/>
                شماره سفارش: {{$product_request_form&&$product_request_form->order?$product_request_form->order->code():""}}

            @endif
        </td>
        <td style="border: none; text-align: right;padding-right: 5px; font-size: 14px">
            تاریخ: {{$form->get_create_date_and_time()}}
            <br/>
            تعداد بسته بندی {{count($form->getPackingFormList())}} عدد
            @php $count_tranport=$form->getTransportCount(); @endphp
            @if($count_tranport > 0)
            <br/>
            تعداد بسته بندی های حمل و نقل
            {{$count_tranport}}
            عدد
            @endif


        </td>
    </tr>
</table>
