<div class="table-responsive">
    <table class="table table-styling">
        <thead>

        <tr>
            <td></td>
            <td>روش پرداخت</td>
            <td style="width: 140px">حداقل درصد قابل قبول</td>
            <td style="width: 140px">حداکثر درصد قابل قبول</td>
            <td style="width: 140px">راس چک (روز)</td>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($payment_method_types as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>

                    <input type="checkbox" class="payment_method" data-id="{{$item->id}}" id="payment_method_type_{{$item->id}}"
                           name="data[payment_method_type][{{$item->id}}]" {{isset($payment_method_min_percentage[$item->id])?"checked":""}} >

                    {{$item->caption}}


                </td>
                <td>
                    <input type="number" class="payment_method_input_{{$item->id}}" style="width: 120px" id="min_percentage_{{$item->id}}"
                           name="data[payment_method_type][min_percentage][{{$item->id}}]"
                           required min="0"
                           value="{{isset($payment_method_min_percentage[$item->id])?$payment_method_min_percentage[$item->id]:""}}"
                    >

                </td>
                <td>
                    <input type="number" class="payment_method_input_{{$item->id}}" style="width: 120px" id="max_percentage_{{$item->id}}"
                           name="data[payment_method_type][max_percentage][{{$item->id}}]"
                           required min="1"
                           value="{{isset($payment_method_max_percentage[$item->id])?$payment_method_max_percentage[$item->id]:""}}"
                    >

                </td>
                <td>
                    @if(in_array($item->id,[50,60,25]))
                        <input type="number" class="payment_method_input_{{$item->id}}"  style="width: 120px" id="max_check_delivery_time_in_days_{{$item->id}}"
                               name="data[payment_method_type][max_check_delivery_time_in_days][{{$item->id}}]"
                               required min="1"
                               value="{{isset($payment_method_max_check_delivery_time_in_days[$item->id])?$payment_method_max_check_delivery_time_in_days[$item->id]:""}}"
                        >
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>

    </table>

</div>

