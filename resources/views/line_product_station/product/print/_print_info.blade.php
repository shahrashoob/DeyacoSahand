
    <div class="content">

        <table style="width: 100%;">
            <tr>
                <td>
                    {{$software_name}}
                </td>

            </tr>


            <tr>
                <td style="border-top: none;padding-right: 3px;">
                    <table style="border: none">

                        <tr>
                            <td style="border: none; text-align: right; font-size: 20px;padding-right: 3px;">
                                کد کالا:
                                {{$product->code??""}}
                                <br/>
                                نام کالا:
                                {{$product->caption??""}}

                                <br/>


{{--                                کد طرح:--}}
{{--                                {{$product->getPropertyValue(220337,"value",true,false)}}--}}
{{--                                &nbsp;--}}
{{--                                &nbsp;--}}
{{--                                کد رنگ:--}}
{{--                                {{$product->getPropertyValue(220338,"value",true,false)}}--}}


                            </td>
                            <td>
                                <div style="font-size: .01px; min-width: 30% ; padding-right: 15px">
                                    {{$qr}}
                                </div>
                            </td>


                        </tr>

                    </table>
                </td>

            </tr>


        </table>
    </div>
    <div style="text-align: center; width: 100%;font-size: 11px">
        سازمان دیجیتال دیاکو
    </div>



