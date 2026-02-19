<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        <tr>
            <td colspan="6">
                {{$software_name}}

                <br/>
                دستور انبارگردانی شماره {{$warehouse_handling->id}}
                - {{$warehouse_handling->warehouse->caption}}
            </td>
        </tr>

        <tr>
            <td>نام کالا</td>
            <td>کد کالا</td>
            <td>تعداد بسته بندی ها</td>
            <td>مقدار کل</td>
        </tr>
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
    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 9px">
    سازمان دیجیتال دیاکو
</div>
