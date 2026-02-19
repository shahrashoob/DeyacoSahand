<div class="content">

    <table style="width: 100%;">
        <tr>
            @if($packing_form->packing_type->label_caption!="")
                <td>
                    {{$packing_form->packing_type->label_caption=="system"?$software_name:$packing_form->packing_type->label_caption}}
                </td>
            @else
                <td style="border-bottom: none"></td>
            @endif

        </tr>


        <tr>
            <td style="border-top: none;padding-right: 3px;">
                <table style="border: none">

                    <tr>
                        <td style="border: none; text-align: right; font-size: 10px;padding-right: 3px;">

                            کد کالا:
                            {{$packing_form->items->first()->product->code}}
                            <br/>

                            نام کالا:
                            {{$packing_form->items->first()->product->caption}}

                            <br/>

                            <span style="font-size: 30px; display: inline; font-weight: bold">
                                {{$packing_form->getFinalAmount()}}
                            </span>
                            <span style="font-size: 14px; display: inline; font-weight: bold">

                                        {{$packing_form->getUnitCaption("unit","caption")}}
                                                                   </span>


                            <br/>


                            @if($packing_form->gross_weight!=0)
                                &nbsp;
                                &nbsp;ناخالص:
                                <span style="font-size: 14px; display: inline">

                                                {{round($packing_form->gross_weight,2)}}

                                            </span>
                                کیلوگرم
                            @endif
                            &nbsp;
                            &nbsp;
                            درجه:
                            {{$packing_form->items->first()->degree->caption}}
                            &nbsp;
                            &nbsp;
                            لات:
                            {{$packing_form->items->first()->lot_number->code}}
                        </td>
                        <td style="border: none; text-align: center;font-size: .01px;width: 80px;padding-left: 3px;">

                            <div style="font-size: 15px">
                                {{$packing_form->getCode()}}
                            </div>
                            <div style="float: left;">
                                {{$qr}}
                            </div>
                        </td>


                    </tr>
                    <tr>
                        <td colspan="2" style="border: none;">
                            @php $applicant=$packing_form->applicant;@endphp
                            @if($applicant)
                                مالک:
                                {{$applicant->fullCaption()}}
                            @endif

                        </td>
                    </tr>

                </table>
            </td>
            <td style="border: none"></td>
        </tr>
        <tr>
            <td style="padding-top: 5px">

                <div style="font-size: 0.02px">
                    {!! $barcode_pin !!}
                </div>
            </td>
            <td style="border: none"></td>
        </tr>


    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
