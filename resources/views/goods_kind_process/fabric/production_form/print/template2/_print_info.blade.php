<div class="content">

    <table style="width: 100%;">

        <tr>

            <td style="border: none;">
                {{$software_name}}
                <br/>
                <div style="font-size: 16px;padding:30px">

                    فرم شاهد (کنترل فرایند) تخصیص
                    {{$allocation->id}}


                </div>
                <br/>
                <br/>
            </td>

        </tr>

        <tr>
            <td style="padding-right: 3px;border:none">
                <table style="border: none">

                    <tr>
                        <td>
                            شماره سفارش:

                        </td>
                        <td style="font-size: 16px">
                            {{isset($machine_allocation->production->order)?$machine_allocation->production->order->code():""}}

                        </td>


                        <td rowspan="6"
                            style="max-width: 100px; font-size: .001px; border:none; text-align: center; padding: 20px ">

                            <div style="float: left;">
                                {{$qr}}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            نام مشتری:
                        </td>
                        <td style="font-size: 16px">
                            {{isset($machine_allocation->production->order->customer)?$machine_allocation->production->order->customer->caption:""}}
                        </td>


                    </tr>

                    <tr>
                        <td>
                            شماره کارت تولید:
                        </td>
                        <td style="font-size: 16px">
                            {{$machine_allocation->production->serial??""}}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            ماشین:
                        </td>
                        <td style="font-size: 16px">
                            {{$machine_allocation->machine->caption}}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            تاریخ تخصیص:
                        </td>
                        <td style="font-size: 16px">
                            {{$machine_allocation->get_datetime()}}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            مقدار تخصیص:
                        </td>
                        <td style="font-size: 16px">
                            {{$machine_allocation->allocation_amount}}

                            {{$machine_allocation->product->unit->caption}}
                        </td>
                    </tr>
                    <tr>

                        <td>
                            شماره لات:

                        </td>
                        <td></td>


                    </tr>

                    <tr>
                        <td>
                            کد کالا:

                        </td>
                        <td colspan="1" style=" font-size: 16px;">

                            {{$machine_allocation->production->product->code}}


                        </td>
                        <td></td>

                    </tr>

                    <tr>
                        <td>
                            نام کالا:

                        </td>
                        <td colspan="2" style=" font-size: 18px;height: 50px">


                            {{$machine_allocation->production->product->caption}}
                        </td>

                    </tr>
                    <tr>
                        <td colspan="3" style="border: none">
                            <br/><br/>
                            <br/>
                        </td>
                    </tr>

                </table>
            </td>

        </tr>
        <tr>

            <td style=" border: none
                        ">

                <table style="border: none">

                    <tr>
                        <td rowspan="2" style="padding: 3px; font-size: 13px">
                            ردیف

                        </td>
                        <td rowspan="2" style="padding: 3px; font-size: 13px">
                            کد ماده اولیه

                        </td>
                        <td rowspan="2" style="padding: 3px; font-size: 13px">
                            نام ماده اولیه

                        </td>
                        <td colspan="2" style="padding: 3px; font-size: 13px">
                            مقدار مورد نیاز

                        </td>

                        <td rowspan="2" style="padding: 3px; font-size: 13px">
                            واحد کالا

                        </td>

                    </tr>
                    <tr>

                        <td style="padding: 3px; font-size: 13px">
                            به ازای واحد کالا

                        </td>
                        <td style="padding: 3px; font-size: 13px">
                            به ازای هر ورودی

                        </td>


                    </tr>
                    @php $row=0; @endphp
                    @foreach($current_input_list as $item)
                        <tr>

                            <td style="padding: 3px; font-size: 13px">{{++$row}}</td>

                            <td style="padding: 3px; font-size: 13px">
                                {{$item->material->code??"***"}}
                            </td>
                            <td style="padding: 3px; font-size: 13px">
                                {{$item->material->caption??"***"}}
                            </td>
                            <td style="padding: 3px; font-size: 13px">
                                {{formatDecimal9($item->amount+0)}}

                            </td>
                            <td style="padding: 3px; font-size: 13px">

                                {{formatDecimal9($item->amount_required)}}
                            </td>

                            <td style="padding: 3px; font-size: 13px">
                                {{$item->material->unit->caption??"***"}}
                            </td>

                        </tr>
                    @endforeach


                </table>
            </td>
        </tr>
        <tr>
            <td style=" text-align: right; border: none">


                توضیحات:
                <table style="border: none;margin-top: 150px">

                    <tr>
                        <td style="padding: 3px;width: 50%; font-size: 13px;text-align: right;">
                            بازرس کنترل کیفیت:
                            <br/>
                            تاریخ:
                        </td>
                        <td style="padding: 3px; font-size: 13px;text-align: right;">
                            سرشیف:
                            <br/>
                            تاریخ:

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
