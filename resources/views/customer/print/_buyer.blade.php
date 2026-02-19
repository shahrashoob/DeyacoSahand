@php $col_span_count=isset($col_span_count)?$col_span_count:0;@endphp
<tr style="background: #9d9d9d;">
    <td colspan="{{$col_span_count +11}}">
        <div style="text-align: center;">
            <h3> مشخصات خریدار </h3>
        </div>
    </td>
</tr>

<tr>
    <td colspan="{{$col_span_count +11}}">
        <div class="border-none table_right">
            <table class="border-none" style="border: none; ">
                <tr>
                    <td style="width: 25%;text-align: right" colspan="2">نام خریدار:
                        {{to_persian($order->customer->customer_type_id == 1?($order->customer->user->firstname." ".$order->customer->user->lastname??""): ($order->customer->caption??""))}}</td>

                    @if($order->customer->customer_type_id == 2)
                        <td style="width: 25%; text-align: right">شناسه ملی:
                            {{to_persian($order->customer->national_code)}}</td>
                        <td style="width: 25%; text-align: right">شماره اقتصادی:
                            {{to_persian($order->customer->economic_number)}}</td>
                    @else

                        <td style="width: 25%; text-align: right">کد ملی:
                            {{to_persian($order->customer->national_code)}}</td>
                        <td style="width: 25%; text-align: right"></td>

                    @endif

                </tr>
                <tr>
                    <td style="text-align: right">استان:
                        {{$order->address->province->caption??""}}</td>
                    <td style="text-align: right">شهر / شهرستان:
                        {{$order->address->city_name??""}}</td>
                    <td style="text-align: right"> کد پستی:
                        {{to_persian($order->address->postal_code??"")}}</td>
                    <td style="text-align: right">شماره تماس :
                        {{to_persian( ($order->address->phone??"")."-".($order->address->mobile??""))}}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right">نشانی:

                        {{to_persian($order->address->address??"")}}
                    </td>
                </tr>

            </table>

        </div>
    </td>
</tr>
