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
            <td style="padding-right: 3px;">
                <table style="border: none">

                    <tr>
                        <td style="border: none; text-align: right; font-size: 11px;padding-right: 3px;">

کد کالا:
                            {{$packing_form->items->first()->product->code}}

<br/>

نام کالا:
                            {{$packing_form->items->first()->product->caption}}

<br/>
                            کد بسته بندی:
                            {{$packing_form->getCode()}}

                            <br/>

                            مقدار:
                                {{$packing_form->getFinalAmount()}}


                            {{$packing_form->getUnitCaption("unit","caption")}}

                            &nbsp;
                            &nbsp;
                            &nbsp;
                            درجه:
                            {{$packing_form->items->first()->degree->caption}}
                            <br/>


                        </td>
                        <td style="border: none; text-align: center;font-size: .01px;width: 80px;padding-left: 3px;">
                            <div style="float: left;">
                                {{$qr}}
                            </div>
                        </td>


                    </tr>


                </table>
            </td>
            <td style="border: none"></td>
        </tr>
        <tr>
            <td style="padding-top: 5px">

                <div style="font-size: 0.02px">
                    {!! $barcode !!}
                </div>
            </td>
            <td style="border: none"></td>
        </tr>


    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
