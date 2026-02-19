<table class="table transparent">

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