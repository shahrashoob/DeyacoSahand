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
            <td rowspan="3" style="border: none">
                <table style="border: none">
                    <tr>
                        <th style="border: none; font-size: 10px" text-rotate="90">هیچ ادعایی پس از برش پذیرفته نمی
                            شود
                        </th>

                    </tr>
                </table>

            </td>
        </tr>


        <tr>
            <td style="border-top: none;padding-right: 3px;">
                <table style="border: none">

                    <tr>
                        <td style="border: none; text-align: right; font-size: 10px;padding-right: 3px;">

                            کد کالا:
                            {{$packing_form->items->first()->product->code}}
                            <br/>
                            <span style="font-size: 9px">
                                نوع بسته بندی:
                                 {{$packing_form->packing_type->fullCaption()}}
                            </span>

                            <br/>

                            <span style="font-size: 40px; display: inline; font-weight: bold">
                                {{$packing_form->getFinalAmount()}}
                            </span>
                            <span style="font-size: 14px; display: inline; font-weight: bold">

                                        {{$packing_form->getUnitCaption("unit","caption")}}
                                                                   </span>


                            <br/>




                            <span style="font-size: 14px; display: inline">

                                                  کد طرح:
                                        {{$packing_form->items->first()->product->getPropertyValue(220337,"value",true,false)}}

                                            </span>



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
                            <table style="border: none;">
                                <tr>
                                    <td style="border: none;text-align: right;font-size: 11px;">
                                        @if($packing_form->items->first() && isset($packing_form->items->first()->production_form_item->production->order) && $packing_form->items->first()->production_form_item->production->order)
                                            ردیف سفارش:
                                            {{$packing_form->items->first()->production_form_item->production->order->getCodeByProductRow(
                                                    $packing_form->items->first()->product_id,
                                                    $packing_form->packing_type_id,

                                                )
                                                }}
                                        @endif
                                    </td>
                                    <td style="border: none;font-size: 11px;text-align: right">
                                        کد رنگ:
                                        {{$packing_form->items->first()->product->getPropertyValue(220338,"value",true,false)}}
                                    </td>

                                    <td style="border: none;font-size: 11px;text-align: right">
                                        درجه:
                                        {{$packing_form->items->first()->degree->caption}}
                                    </td>
                                    <td style="border: none;font-size: 11px;text-align: right">
                                        لات:
                                        {{$packing_form->items->first()->lot_number->code}}
                                    </td>
                                </tr>

                            </table>

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
