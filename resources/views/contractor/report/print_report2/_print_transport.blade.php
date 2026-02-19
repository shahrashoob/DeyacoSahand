<div class="content" style="border: #0b0b0b 3px solid; padding:3px">
    <div style="text-align: center; padding: 3px">
        {{$header_text}}
        <br/>
        گزارش تولید پیمانکاران به تفکیک کد کالا
        <hr style="margin: 0px; padding: 0px"/>
    </div>
    <table style="width: 100%; border:none">

        <tr>

            <td style="border:none">
                    از تاریخ:
                {{$start_date}}
            </td>
            <td style="border:none">

                تا تاریخ:
                {{$end_date}}
            </td>
        </tr>
        <tr>

            <td style="border:none">
                    متن جستجو:
                {{$search}}
            </td>
            <td style="border:none">

                شماره سفارش:
                {{$order_search}}
            </td>
        </tr>


    </table>
    <br/>
    <br/>


        <table>


                <tr>
                    <th>ردیف</th>
                    <th>
                       دستور پیمان
                    </th>
                    <th>
                        کد کالا
                    </th>
                    <th>
                        عنوان کالا
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
            @php $sum_amount=0;@endphp
            @php $sum_sub_amount=0;@endphp
            @php $sum_packing_form_count=0;@endphp
            @foreach($list as $item)
                <tr>
                    <td>{{$row++}}</td>
                    <td>
                        {{$item->production_form_item->production->serial??""}}
                    </td>
                    <td>
                        {{$item->product->code}}
                    </td>
                    <td>
                        {{$item->product->caption}}
                    </td>
                    <td >
                        {{$item->packing_form_count}}
                    </td>
                    <td >
                        {{$item->amount}}
                    </td>
                    <td >
                        {{$item->sub_amount}}
                    </td>


                </tr>

                @php $sum_amount+=$item->amount; $sum_sub_amount+=$item->sub_amount; $sum_packing_form_count+=$item->packing_form_count; @endphp

            @endforeach

                <tr>
                    <td colspan="4">
                        جمع کل
                    </td>
                    <td>
                       {{$sum_packing_form_count}}
                    </td>
                    <td>
                       {{$sum_amount}}
                    </td>
                    <td>
                        {{$sum_sub_amount}}
                    </td>
                </tr>

        </table>
        <br/>


</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
