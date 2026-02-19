<table class="table table-styling center">
    <thead>
    <tr>

        <th>نام کالا</th>
        <th>کد کالا</th>
        <th>تعداد بسته بندی ها</th>
        <th>مقدار کل</th>
    </tr>

    </thead>
    <tbody>
    @php $row=$first_row;@endphp
    @foreach($product_list as $item)
        <tr>
            <td>
                {{$item->product->code}}
            </td>
            <td>
                {{$item->product->caption}}
            </td>
            <td>{{isset($packing_form_data[$item->product_id])?$packing_form_data[$item->product_id]->count:0}}</td>
            <td>{{isset($packing_form_data[$item->product_id])?$packing_form_data[$item->product_id]->sum_final_amount:0}}</td>


        </tr>
    @endforeach
    </tbody>

</table>