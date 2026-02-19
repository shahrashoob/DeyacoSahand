<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%;">
        <tr>
            @if($packing_form->packing_type->label_caption!="")
                <td >
                    {{$packing_form->packing_type->label_caption=="system"?$software_name:$packing_form->packing_type->label_caption}}
                </td>
            @else
                <td style="border-bottom: none"></td>
            @endif
            <td  rowspan="3" style="border: none">
                <table style="border: none">
                    <tr>
                        <th style="border: none; font-size: 10px" text-rotate="90">هیچ ادعایی پس از برش پذیرفته نمی شود</th>

                    </tr>
                </table>

            </td>
        </tr>


        <tr>
            <td  style="border-top: none;padding-right: 3px;">
                <table style="border: none">

                    <tr>
                        <td style="border: none; text-align: right; font-size: 11px;padding-right: 3px;">

                            کد کالا:
                            {{$packing_form->items->first()->product->code}}
                            <br/>
                            شماره: {{$packing_form->getCode()}}

                            @php $row=1;@endphp
                            @if(count($packing_form->items)>1)
                                <br/>
                                @foreach($packing_form->items as $item)
                                    آیتم
                                    {{$row++}}:
                                    {{$item->final_amount}}
                                    {{$packing_form->getUnitCaption("unit","caption")}},
                                @endforeach
                            @endif

                            <br/>
                            سریال تولید:
                            {{$packing_form->items->first()->production_form_item->production->serial??""}}

                            <table style="border: none;">
                                <tr>
                                    <td style="border: none;text-align: right;font-size: 11px;">
                                        کد طرح:
                                        {{$packing_form->items->first()->product->getPropertyValue(220337,"value",true,false)}}
                                    </td>
                                    <td style="border: none;font-size: 11px;text-align: right">
                                        کد رنگ:
                                        {{$packing_form->items->first()->product->getPropertyValue(220338,"value",true,false)}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: none;font-size: 11px;text-align: right">
                                        درجه:
                                        {{$packing_form->items->first()->degree->caption}}
                                    </td>
                                    <td style="border: none;font-size: 11px;text-align: right">
                                        لات:
                                        {{$packing_form->items->first()->lot_number->code}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: none;font-size: 11px;text-align: right">
                                        {{$packing_form->getUnitCaption("unit","measurement",":")}}
                                        <span style="font-size: 16px; display: inline">
                                            {{$packing_form->getFinalAmount()}}
                                        </span>
                                        {{$packing_form->getUnitCaption("unit","caption")}}
                                    </td>
                                    <td style="border: none;font-size: 11px;text-align: right">
                                        @if($packing_form->getUnitCaption("sub_unit","measurement",":"))
                                            {{$packing_form->getUnitCaption("sub_unit","measurement",":")}}
                                            <span style="font-size: 16px; display: inline">
                                                {{$packing_form->getSubAmount()}}
                                            </span>
                                            {{$packing_form->getUnitCaption("sub_unit","caption")}}
                                        @endif
                                    </td>
                                </tr>
                            </table>

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
            <td  style="padding-top: 5px">

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
