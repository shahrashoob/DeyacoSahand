<div class="content" style="border: #0b0b0b 3px solid;">
    <div style="text-align: center; padding: 3px">
        {{$header_text}}
        <br/>
        گزارش تولید پیمانکار
        <hr style="margin: 0px; padding: 0px"/>
    </div>

    <table style="width: 100%; border:none; margin: 5px">


        <tr>
            <td style="border:none;text-align: right">
                پیمانکار:
                {{$contractor_allocation->contractor->caption}}
            </td>
            <td style="border:none; ">
                سریال دستور پیمان:
                {{$contractor_allocation->production->serial()}}
            </td>
        </tr>
        <tr>
            <td style="border:none; text-align: right">
                کد کالا:
                {{$contractor_allocation->product->code}}
            </td>
            <td style="border:none; text-align: right">
                طرح:
                {{$contractor_allocation->product->getPropertyValue(220337,"value",true,false)}}
                &nbsp;
                &nbsp;
                &nbsp; &nbsp;
                &nbsp;
                &nbsp;
                رنگ:
                {{$contractor_allocation->product->getPropertyValue(220338,"value",true,false)}}
            </td>

        </tr>
        <tr>
            <td colspan="2" style="border:none; text-align: right">
                نام کالا:
                {{$contractor_allocation->product->caption}}</td>
        </tr>
        <tr>
            <td colspan="2" style="border:none; text-align: right">
                نوع بسته بندی(ها):
                @foreach($contractor_allocation->production->packing_types as $production_packing_type)
                    {{$production_packing_type->packing_type->caption}} ,
                @endforeach
            </td>

        </tr>


    </table>

    <table style="margin: 5px">
        <tr>
            <th>ردیف</th>
            <th>کد بسته بندی</th>
            <th>تعداد بسته بندی<br/> فرعی/اقلام</th>
            <th>متراژ</th>
            <th>وزن</th>
        </tr>

        @php $row=1;$amount=0;$sub_amount=0;@endphp
        @php $sum_amount=0; @endphp
        @php $sum_sub_amount=0;@endphp
        @foreach($contractor_allocation->getContractorPackingForm() as $item)
            <tr>
                <td>{{$row++}}</td>
                <td>
                    {{$item->packing_form->getCode()}}

                </td>


                <td>{{$item->packing_form->items()->count()}}</td>
                <td>{{$amount=$item->packing_form->getAllAmount("final_amount")}}</td>
                <td>{{$sub_amount=$item->packing_form->getAllAmount("sub_amount")}}</td>
                @php
                    $sum_amount+=$amount;
                    $sum_sub_amount+=$sub_amount;
                @endphp
            </tr>
        @endforeach
        <tr>
            <td colspan="3">جمع کل</td>
            <td>{{$sum_amount}}</td>
            <td>{{$sum_sub_amount}}</td>
        </tr>
        <tr>
            <td colspan="5">
                تاریخ و زمان چاپ:
                {{$date_time}}
            </td>
        </tr>
    </table>
    <br/>


</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
