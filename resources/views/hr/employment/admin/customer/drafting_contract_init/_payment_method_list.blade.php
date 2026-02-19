<div class="table-responsive">
    <table class="table table-styling center">
        <thead>

        <tr>
            <th>روش پرداخت</th>
            <th >حداقل درصد قابل قبول</th>
            <th >حداکثر درصد قابل قبول</th>
            <th >راس چک (روز)</th>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($payment_method_types as $item)
            <tr>
                @if(isset($payment_method_min_percentage[$item->id]))
                    <td>{{$item->caption}}</td>
                    <td>{{$payment_method_min_percentage[$item->id]}}</td>
                    <td>{{$payment_method_max_percentage[$item->id]}}</td>
                    <td>
                        @if(in_array($item->id,[50,60,25]))

                            {{$payment_method_max_check_delivery_time_in_days[$item->id]}}
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>

    </table>

</div>


