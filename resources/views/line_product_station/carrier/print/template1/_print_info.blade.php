<div class="content">

    <table style="width: 100%;height: 100px; border:none; ">

{{--        <tr>--}}
{{--            <td style="border: none; ">--}}

{{--                <div style="font-size: 110px;text-decoration: underline; ">--}}
{{--                    {{$carrier->code}}--}}
{{--                </div>--}}
{{--            </td>--}}
{{--        </tr>--}}
        <tr>

            <td style="border: none; "> <div style="font-size: 18px; ">
                    {{$carrier->carrier_type->caption}}
                    <br/>
                    سازمان دیجیتال دیاکو
                    <div style="font-size: 14px;  ">
                        {{$carrier->weight??0}} کیلوگرم
                    </div>
                </div>
                <br/>
                <br/>
            </td>

        </tr>
        <tr>
            <td style="border: none; ">
                <div style="font-size: 180px;text-decoration: underline;  ">
                    {{$carrier->code}}
                </div>

            </td>
        </tr>
        <tr>
            <td style="border: none; ">
                <div style="font-size: 14px;  ">
                    {{$carrier->code}}
                </div>

            </td>
        </tr>



    </table>
</div>
