@switch($product->supply_type_id )
    @case(1)
        <div class="table-responsive center" style="font-size: 12px">
            <table class="table table-styling">
                <thead>
                <tr>
                    <th></th>
                    <th>#</th>

                    <th>خط ورودی</th>
                    <th>ایستگاه کاری</th>
                    <th>عملیات در ایستگاه کاری</th>
                    <th>عملیات فرعی</th>
                    <th>مقدار</th>
                    <th>تعداد</th>
                    <th>درصد استفاده</th>

                </tr>

                </thead>
                <tbody>

                @php $row=1; @endphp
                @foreach($bom_item_replace as $item)
                    <tr>
                        <td>
                            <input name="bom_item[{{$item->id}}]" type="checkbox" checked>
                        </td>
                        <td>{{$row++}}</td>
                        <td>{{$item->input_line_code}}</td>
                        <td>{{$item->station->caption??""}}</td>
                        <td>{{$item->station_operation->caption??""}}</td>
                        <td>{{$item->station_sub_operation->caption??""}}</td>

                        <td>{{$item->amount??""}}</td>
                        <td>{{$item->number}}</td>
                        <td>{{$item->percent_of_use}}</td>

                    </tr>
                @endforeach
                </tbody>

            </table>

        </div>

        @break



@endswitch