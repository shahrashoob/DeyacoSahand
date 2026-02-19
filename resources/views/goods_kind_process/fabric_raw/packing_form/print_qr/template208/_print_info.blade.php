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
{{--            <td rowspan="3" style="border: none">--}}
{{--                <table style="border: none">--}}
{{--                    <tr>--}}
{{--                        <th style="border: none; font-size: 10px" text-rotate="90">هیچ ادعایی پس از برش پذیرفته نمی--}}
{{--                            شود--}}
{{--                        </th>--}}

{{--                    </tr>--}}
{{--                </table>--}}

{{--            </td>--}}
        </tr>


        <tr>
            <td style="border-top: none;padding-right: 3px;">
                <table style="border: none">

                    <tr>
                        <td   style="border: none; text-align: right; font-size: 20px;padding-right: 3px;">
                             کد کالا:
                            {{$packing_form->items->first()->product->code}}

                        </td>
                        <td rowspan="3" style="border: none; text-align: center">

                            <div style="font-size: .01px; position: fixed; left:0 ">
                                {{$qr}}
                            </div>
                            {{$packing_form->getCode()}}
                        </td>
                    </tr>
                    <tr>
                        <td  style="border: none; text-align: right; font-size: 11px;">
نام کالا:



                            {{$packing_form->items->first()->product->caption}}
                            <br/>

                            تاریخ:
                            {{$packing_form->get_create_date_and_time('Y/m/d')}}
                            <br/>


                               فرم ورود به انبار:
                                 {{$packing_form->form->code??"---"}}



                        </td>


                    </tr>
                    <tr>
                        <td
                            style="border: none; text-align: right; font-size: 11px;padding-right: 3px;">
                            <table style="border: none; text-align: right;">
                                <tr>
                                    <td style="font-size: 12px;border: none; text-align: right;">

                                        @if($packing_form->items->first() && isset($packing_form->items->first()->production_form_item->production->order) && $packing_form->items->first()->production_form_item->production->order)
                                             سفارش:
                                            {{$packing_form->items->first()->production_form_item->production->order->getCodeByProductRow(
                                                    $packing_form->items->first()->product_id,
                                                    $packing_form->packing_type_id,

                                                )
                                                }}
                                        @if(isset($packing_form->items->first()->production_form_item->production->order->customer))
                                                (
                                                {{$packing_form->items->first()->production_form_item->production->order->customer->code??""}}
                                                )
                                        @endif

                                        @endif
                                    </td>
                                    <td style="font-size: 12px;border: none; text-align: center;">
                                        <span style="font-size: 30px; display: inline; font-weight: bold">
                                {{$packing_form->getFinalAmount(2)}}
                            </span>
                                        <span style="font-size: 14px; display: inline; font-weight: bold">

                                        {{$packing_form->getUnitCaption("unit","caption")}}
                            </span>
                                    </td>
                                </tr>
                            </table>




                        </td>


                    </tr>


                    <tr>
                        <td colspan="2" style="border: none;">
                            <table style="border: none;">
                                <tr>
{{--                                    <td style="border: none;text-align: right;font-size: 11px;">--}}

{{--                                        کد طرح:--}}
{{--                                        {{$packing_form->items->first()->product->getPropertyValue(220219,"value",true,false)}}--}}

{{--                                    </td>--}}
{{--                                    <td style="border: none;font-size: 11px;text-align: right">--}}
{{--                                        کد رنگ:--}}
{{--                                        {{$packing_form->items->first()->product->getPropertyValue(220338,"value",true,false)}}--}}
{{--                                    </td>--}}

                                    <td style="border: none;font-size: 11px;text-align: right">
                                        درجه:
                                        {{$packing_form->items->first()->degree->caption}}
                                    </td>

                                    <td style="border: none;font-size: 15px;text-align: right; padding-left: 20px; padding-bottom: 2px">
                                        عرض:
                                        <span style="border: none; font-size: 15px; font-weight: bolder">
                                        {{$packing_form->items->first()->product->getPropertyValue(220263,"value",true,false)}}
                                        </span>

                                    </td>
                                    <td style="border: none;font-size: 11px;text-align: right">
                                        لات:
                                        {{$packing_form->items->first()->lot_number->code??""}}
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                </table>
            </td>
            <td style="border: none"></td>
        </tr>
{{--        <tr>--}}
{{--            <td style="padding-top: 5px">--}}

{{--                <div style="font-size: 0.02px">--}}
{{--                    {!! $barcode !!}--}}
{{--                </div>--}}
{{--            </td>--}}
{{--            <td style="border: none"></td>--}}
{{--        </tr>--}}
        <tr>
            <td  style="border: none; text-align: center; ">
                <div style="font-size: 0.02px">
                    {!! $barcode_pin !!}
                </div>
            </td>
        </tr>


    </table>
</div>
{{--<div style="text-align: center; width: 100%;font-size: 11px">--}}
{{--    سازمان دیجیتال دیاکو--}}
{{--</div>--}}
