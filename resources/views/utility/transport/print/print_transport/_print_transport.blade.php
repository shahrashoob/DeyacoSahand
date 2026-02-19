<div class="content" style="border: #0b0b0b 3px solid; padding:3px">
    <div style="text-align: center; padding: 3px">
        {{$header_text}}
        <br/>
        گزارش ارسال بار به تفکیک بسته بندی حمل و نقل
        <hr style="margin: 0px; padding: 0px"/>
    </div>
    <table style="width: 100%; border:none">


        <tr>
            <td style="border:none">
                نام مشتری:
                {{$transport->customer_caption}}</td>

            <td style="border:none">شماره بار:
                {{$transport->getCode()}}</td>



            <td style="border:none">تاریخ:
                {{$transport->created_date_time()}}</td>

        </tr>
        <tr>

            <td style="border:none">تعداد بسته بندی بار:
                {{$transport->items()->count()}}</td>

            <td style="border:none">متراژ کل:
                {{$transport->getAmount("final_amount")}}</td>

            <td style="border:none">وزن کل:
                {{$transport->getAmount("sub_amount")}}</td>
        </tr>


    </table>
    <br/>
    <br/>
    @foreach($transport->items as $transport_item)

        <table>
            <tr>
                <td colspan="7" style="font-size: 16px; font-weight: bold">
                    {{$transport_item->code()}}
                </td>
            </tr>
            @if($transport_item->transport_packing_list()->first())
                <tr>
                    <th>ردیف</th>
                    <th>
                        کد بسته بندی
                    </th>
                    <th style="width: 60px">
                        کد کالا
                    </th>
                    <th>
                        نام کالا
                    </th>
                    <th style="width: 40px">
                        درجه
                    </th>
                    <th style="width: 50px">
                        {{isset($unit_caption)?$unit_caption:$unit_caption=$transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("unit","measurement")}}
                    </th>
                    <th style="width: 50px">
                        {{isset($sub_unit_caption)?$sub_unit_caption:$sub_unit_caption=$transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("sub_unit","measurement")}}

                    </th>


                </tr>
            @endif
            @php $row=1;@endphp
            @foreach($transport_item->transport_packing_list as $item)
                <tr>
                    <td>{{$row++}}</td>
                    <td>
                        {{$item->packing_form->code}}
                    </td>
                    <td>
                        {{$item->packing_form->items()->first()->product->code}}
                    </td>
                    <td>
                        {{$item->packing_form->items()->first()->product->caption}}
                    </td>
                    <td>
                        {{$item->packing_form->items()->first()->degree->caption}}
                    </td>
                    <td>
                        {{$item->packing_form->getFinalAmount()}}
                    </td>
                    <td>
                        {{$item->packing_form->getAllAmount("sub_amount")}}
                    </td>


                </tr>
            @endforeach

            @if($transport_item->transport_packing_list()->first())
                <tr>
                    <td colspan="5">
                        جمع کل
                    </td>
                    <td>
                        {{$transport_item->getAmount("final_amount")}}
                    </td>
                    <td>
                        @if($transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("sub_unit","measurement","کل:"))
                            {{$transport_item->getAmount("sub_amount")}}
                        @endif
                    </td>
                </tr>
            @endif
        </table>
        <br/>
    @endforeach


</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
