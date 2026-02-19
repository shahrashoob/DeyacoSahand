
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
                            <td style="border: none; text-align: right; font-size: 14px;padding-right: 3px;">
                                شماره سفارش:
                                {{$contractor_allocation->production->order->code??""}}

                                <br/>
                                سریال دستور پیمان:
                                {{$contractor_allocation->production->serial??""}}
                                <br/>

                                کد طرح:
                                {{$contractor_allocation->production->product->getPropertyValue(220337,"value",true,false)}}
                                &nbsp;
                                &nbsp;
                                کد رنگ:
                                {{$contractor_allocation->production->product->getPropertyValue(220338,"value",true,false)}}


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

<div style="border-top:3px dotted;margin: 6px" ></div>

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
                            <td style="border: none; text-align: right; font-size: 14px;padding-right: 3px;">
                                شماره سفارش:
                                {{$contractor_allocation->production->order->code??""}}
                                
                                <br/>
                                سریال دستور پیمان:
                                {{$contractor_allocation->production->serial??""}}
                                <br/>

                                کد طرح:
                                {{$contractor_allocation->production->product->getPropertyValue(220337,"value",true,false)}}
                                &nbsp;
                                &nbsp;
                                کد رنگ:
                                {{$contractor_allocation->production->product->getPropertyValue(220338,"value",true,false)}}


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

