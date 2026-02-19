<div class="content">

    <table style="width: 100%;">

        <tr>

            <td style="border: none;">
                {{$software_name}}

            </td>

        </tr>

        <tr>
            <td style="padding-right: 3px;border:none">
                <table style="border: none">

                    <tr>
                        <td>
                            فرم تولید
                        </td>
                        <td>
                            {{$production_form->getCode()}}
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
                            ماشین:
                        </td>
                        <td>
                            {{$production_form->machine->caption}}
                        </td>

                    </tr>
                    <tr>
                        <td>
                            تاریخ ایجاد
                        </td>
                        <td>
                            {{$production_form->get_datetime()}}
                        </td>


                    </tr>

                    <tr>
                        <td>
                            مقدار کل:
                        </td>
                        <td>
                            {{$production_form->getAmount()}}
                        </td>
                    </tr>


                    <tr>
                        <td>
                            شماره حامل:
                        </td>
                        <td>
                            @if($production_form->carrier)
                                {{$production_form->carrier->code??""}}
                                ({{$production_form->carrier->carrier_type->caption??""}})
                            @endif
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
                        <td style="padding: 3px; font-size: 13px">
                            ردیف

                        </td>
                        <td style="padding: 3px; font-size: 13px">
                            باند

                        </td>
                        <td style="padding: 3px; font-size: 13px">
                            کارت تولید

                        </td>
                        <td style="padding: 3px; font-size: 13px">
                            نام و کد کالا

                        </td>
                        <td style="padding: 3px; font-size: 13px">
                            مقدار نهایی

                        </td>
                        <td style="padding: 3px; font-size: 13px">


                        </td>

                    </tr>
                    @php $row=0; @endphp
                    @foreach($production_form->items as $item)
                        <tr>

                            <td style="padding: 3px; font-size: 13px">{{++$row}}</td>

                            <td style="padding: 3px; font-size: 13px">
                                {{$item->band_code}}
                            </td>
                            <td style="padding: 3px; font-size: 13px">
                                {{$item->production->serial}}
                            </td>
                            <td style="padding: 3px; font-size: 13px">
                                {{$item->production->product->caption}}
                                <br/>
                                {{$item->production->product->code}}
                            </td>
                            <td style="padding: 3px; font-size: 13px">
                                {{formatDecimal9($item->final_amount+0)}}
                                {{$item->production->product->unit->caption}}

                            </td>
                            <td style="padding: 3px; font-size: 13px">
                                @if($item->product->sub_unit)
                                    {{formatDecimal9($item->sub_amount)}}
                                    {{$item->product->sub_unit->caption}}
                                @endif

                            </td>

                        </tr>
                    @endforeach


                </table>
            </td>
        </tr>


    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
