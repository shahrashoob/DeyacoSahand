<div class="content" style="border: #0b0b0b 3px solid; padding:3px">
    <div style="text-align: center; padding: 3px">
        {{$header_text}}
        <br/>
        گزارش بسته بندی درخواست به تفکیک کد کالا
        <hr style="margin: 0px; padding: 0px"/>
    </div>
    <table style="width: 100%; border:none;">

        <tr>
            <td style="border:none;text-align: right">
                نام درخواست کننده:
                {{$product_request_form->applicant->fullCaption()}}</td>

            <td style="border:none">شماره درخواست:
                {{$product_request_form->getCode()}}</td>





        </tr>
        <tr>
            <td style="border:none">تاریخ:
                {{$product_request_form->get_create_date_and_time()}}</td>
            <td style="border:none;text-align: right">
                تعداد بسته بندی حمل و نقل:
                {{$product_request_form->transport_items()->count()}}
            </td>
        </tr>


    </table>
    <br/>
    <br/>


        <table>

                <tr>
                    <th style="width: 20px">ردیف</th>
                    <th style="width: 80px">
                        کد کالا
                    </th>
                    <th style="width: 200px">
                        نام کالا
                    </th>
                    <th style="width: 60px">
                        تعداد بسته بندی
                    </th>
                    <th style="width: 60px">
                        متراژ
                    </th>
                    <th style="width: 60px">
                       وزن
                    </th>

                </tr>
            @php $row=1;@endphp
            @php $amount=0;$sub_amount=0;$packing_count=0;@endphp
            @foreach($list as $item)
                <tr>
                    <td>{{$row++}}</td>
                    <td>
                        {{$item->product->code}}
                    </td>
                    <td>
                        {{$item->product->caption}}
                    </td>
                    <td>
                        {{$item->packing_form_count}}
                    </td>
                    <td>
                        {{$item->amount}}
                    </td>
                    <td>
                        {{$item->sub_amount}}
                    </td>
                    @php
                        $sub_amount+=$item->sub_amount;
                        $amount+=$item->amount;
						$packing_count+=$item->packing_form_count;
                    @endphp
                </tr>
            @endforeach


                <tr>
                    <td colspan="3">
                        جمع کل
                    </td>
                    <td>
                        {{$packing_count}}
                    </td>
                    <td>
                        {{$amount}}
                    </td>
                    <td>
                        {{$sub_amount}}
                    </td>
                </tr>

        </table>
        <br/>


</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
