<div class="table-responsive">
    <table class="table table-styling ">
        <thead>

        <tr>
            <th>روش پرداخت</th>
            <th style="width: 140px">حداقل درصد قابل قبول</th>
            <th style="width: 140px">حداکثر درصد قابل قبول</th>
            <th style="width: 140px">راس چک (روز)</th>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($payment_method_types as $item)
            @if(isset($data_customer_default_setting["payment_method_type"][$item->id]['enable'])&&
                    !$data_customer_default_setting["payment_method_type"][$item->id]["enable"])
                <tr>

                    <td>

                        <input type="checkbox" class="payment_method" data-id="{{$item->id}}"
                               id="payment_method_type_{{$item->id}}"
                               name="data[payment_method_type][{{$item->id}}][checked]" {{isset( $data_customer_default_setting["payment_method_type"][$item->id]['checked']) &&
                            $data_customer_default_setting["payment_method_type"][$item->id]['checked']?"checked":""}}
                        >

                        {{$item->caption}}


                    </td>
                    <td>
                        <input type="number" class="payment_method_input_{{$item->id}}" style="width: 120px"
                               id="min_percentage_{{$item->id}}"
                               name="data[payment_method_type][{{$item->id}}][min_percentage]"
                               required min="0"
                               value="{{isset( $data_customer_default_setting["payment_method_type"][$item->id]['min_percentage'])?
                            $data_customer_default_setting["payment_method_type"][$item->id]['min_percentage']:""}}"

                        >

                    </td>
                    <td>
                        <input type="number" class="payment_method_input_{{$item->id}}" style="width: 120px"
                               id="max_percentage_{{$item->id}}"
                               name="data[payment_method_type][{{$item->id}}][max_percentage]"
                               required min="1"
                               value="{{isset( $data_customer_default_setting["payment_method_type"][$item->id]['max_percentage'])?
                             $data_customer_default_setting["payment_method_type"][$item->id]['max_percentage']:""}}"

                        >

                    </td>
                    <td>
                        @if(in_array($item->id,[50,60,25]))
                            <input type="number" class="payment_method_input_{{$item->id}}" style="width: 120px"
                                   id="max_check_delivery_time_in_days_{{$item->id}}"
                                   name="data[payment_method_type][{{$item->id}}][max_check_delivery_time_in_days]"
                                   required min="1"
                                   value="{{isset( $data_customer_default_setting["payment_method_type"][$item->id]['max_check_delivery_time_in_days'])?
                              $data_customer_default_setting["payment_method_type"][$item->id]['max_check_delivery_time_in_days']:""}}"
                            >
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>

    </table>

</div>

