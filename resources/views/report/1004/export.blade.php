<table>
    <thead>
    <tr>
        <th colspan="12" style="text-align: center;font-size: 16px;background: #bfbfbf">
              کاردکس   {{$product->caption}}
        </th>
    </tr>
    <tr>
        <th colspan="12" style="text-align:center;font-size: 14px; border:2px solid #000000;background: #bfbfbf">
            کد کالا
            :
            {{$product->code}}
        </th>

    </tr>
    <tr>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">ردیف</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">تاریخ</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">ساعت</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">نوع تراکنش</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">درجه </th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">همبافت</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">حامل</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">بسته بندی</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">انبار</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">وارده</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">صادره</th>
        <th style="font-weight:bold;text-align:center; border:2px solid #000000;background: #bfbfbf">موجودی</th>
    </tr>

    </thead>
    <tbody>
    @php $i=1;@endphp

    @foreach($list as $item)
        @php $init_inventory+=$item->input-$item->output;@endphp
        <tr>
            <td style="text-align:center; border:2px solid #000000;">{{$i++}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->get_create_date()}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->get_create_time()}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->trans_kind_item->caption??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->degree->caption??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->lot_number->code??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->carrier?$item->carrier->getCaption():"---"}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->packing_type?$item->packing_type->fullCaption():"---"}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->warehouse->caption??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->input}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->output}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$init_inventory}}</td>


        </tr>


    @endforeach
    </tbody>

</table>
