@if($order->selling_type_id==1)
    <tr style="background: #9d9d9d;">
        <td colspan="{{$col_span_count +11}}">
            <div style="text-align: center;">
                <h3> مشخصات فروشنده </h3>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="{{$col_span_count +11}}">
            <div class="border-none table_right">
                <table class="border-none" style="border: none">
                    <tr>
                        <td style="width: 25%;text-align: right" colspan="2">نام فروشنده:
                            {{$seller}}
                        </td>
                        <td style="width: 25%;text-align: right">شناسه ملی:
                            {{to_persian($national_code)}}
                        </td>
                        <td style="width: 25%;text-align: right">شماره اقتصادی:
                            {{to_persian($economic_number)}}
                        </td>
                        <td style="width: 25%;"></td>
                    </tr>

                </table>

            </div>
        </td>
    </tr>


@endif
