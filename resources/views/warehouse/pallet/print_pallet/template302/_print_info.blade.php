<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">

            <tr>
                <td colspan="3" style="font-size: 14px; padding-top: 3px">
                    {{$software_name}}
                </td>
            </tr>

        <tr>
            <td colspan="3" style=" text-align: center; font-size: 20px;padding-top: 3px;padding-bottom: 6px">

                پالت  {{$pallet->id}}
            </td>
        </tr>

        <tr>
            <td style="font-size: 12px">
                تاریخ
            </td>
            <td style="font-size: 12px">

                {{$pallet->get_created_datetime()}}
            </td>
            <td rowspan="2" style="border: none; text-align: center;padding-right: 5px; font-size: 13px;width: 30%">

                <div style="font-size: .01px; min-width: 30% ; padding-right: 15px">
                    {{$qr}}
                </div>

            </td>

        </tr>
        <tr>
            <td style="font-size: 13px; width:25% ">
                کد پالت
            </td>
            <td style="font-size: 13px;width:30%">
                {{$pallet->getCodeNumber()}}


            </td>

        </tr>
{{--        <tr>--}}
{{--            <td style="font-size: 13px; width:25% ">--}}
{{--               تعداد بسته بندی--}}
{{--            </td>--}}
{{--            <td style="font-size: 13px;width:30%">--}}
{{--                {{$pallet->items->count()}} عدد--}}


{{--            </td>--}}

{{--        </tr>--}}




        <tr>
            <td colspan="3">
                <div style="font-size: 0.02px">
                    {!! $barcode !!}
                </div>
                <br/>
            </td>
        </tr>

    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
