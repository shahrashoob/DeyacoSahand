<table style="border: none">
    <tr>
        <td style="padding-right: 150px; border: none">
            <h2> {{$caption}} </h2>
        </td>
        <td style=" text-align: left !important;font-size: 12px;float: left; width: 150px; border: none">
            @if($order->selling_type_id==1)
                شماره سفارش: {{to_persian($order->code())}}
                @if(isset($form))
                    <br/>
                    برگ خروج: {{to_persian($form->code)}}
                @endif
                <br/>
            @endif

            @if(isset($form))
                تاریخ برگ خروج: {{to_persian($form->get_create_date())}}
                @php $transaction_item=$form->get_transaction_time(); @endphp
                @if($transaction_item)
                    <br/>
                    تاریخ تراکنش:
                        {{to_persian($transaction_item)}}

                @endif
            @else
                    تاریخ: {{to_persian($order->create_date())}}
            @endif
        </td>
    </tr>
</table>
