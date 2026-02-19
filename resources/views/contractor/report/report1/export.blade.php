<html>

<table style=" ">
    <thead>
    <tr>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد کالا</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام کالا</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">دستور پیمان</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام پیمانکار</th>

        <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد بسته بندی</td>
        <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">نوع بسته بندی</td>



        <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد طرح</td>
        <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد رنگ</td>

        <td style="text-align:center; border:2px solid #000000;background: #bfbfbf"> درجه</td>
        <td style="text-align:center; border:2px solid #000000;background: #bfbfbf"> لات</td>
        <td style="text-align:center; border:2px solid #000000;background: #bfbfbf"> متراژ</td>
        <td style="text-align:center; border:2px solid #000000;background: #bfbfbf"> وزن</td>
        <td style="text-align:center; border:2px solid #000000;background: #bfbfbf"> تاریخ بسته بندی</td>

    </tr>

    </thead>
    <tbody>
    @foreach($list as $item)
        @if(count($item->packing_form->items ) >0)
        <tr>

            @php $packing_form_item=$item->packing_form->items()->first();@endphp
            <td style="text-align:center; border:2px solid #000000;">{{$packing_form_item->product->code}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$packing_form_item->product->caption}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->contractor_allocation->production->serial}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->contractor_allocation->contractor->caption}}</td>

            <td style="text-align:center; border:2px solid #000000;"> {{$item->packing_form->code??""}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{$item->packing_form->packing_type->caption??""}}</td>


            <td style="text-align:center; border:2px solid #000000;">{{$packing_form_item->product->getPropertyValue(220337,"value",true,false)}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$packing_form_item->product->getPropertyValue(220338,"value",true,false)}}</td>

            <td style="text-align:center; border:2px solid #000000;"> {{$packing_form_item->degree->caption}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{$packing_form_item->lot_number->code}}</td>

            <td style="text-align:center; border:2px solid #000000;">{{$item->packing_form->getAmount("final_amount")}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->packing_form->getAmount("sub_amount")}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->packing_form->get_create_date_and_time()}}</td>


        </tr>
        @endif
    @endforeach
    </tbody>

</table>
</html>
