<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        @if($packing_form->packing_type->label_caption!="")
            <tr>
                <td colspan="4" style="font-size: 15px; padding-top: 3px">
                    {{$packing_form->packing_type->label_caption=="system"?$software_name:$packing_form->packing_type->label_caption}}
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="4" style=" text-align: center; font-size: 20px;padding-top: 3px;padding-bottom: 6px">
                @foreach($packing_form->items as $item)
                    {{$item->product->caption??"***"}}
                @endforeach
            </td>
        </tr>

        <tr>
            <td style="font-size: 12px">
                شماره بسته بندی
            </td>
            <td style="font-size: 14px; font-weight: bold">

                {{$packing_form->getCode()}}
            </td>
            <td colspan="2" rowspan="6"
                style="border: none; text-align: center;padding-right: 5px; font-size: 13px;width: 45%">


                <br/>



                <div style="font-size: .01px; min-width: 30% ; padding-right: 15px">
                    {{$qr}}
                </div>


                <br/>
                @foreach($packing_form->items as $item)
                    {{$item->product->code??"***"}}
                @endforeach

            </td>

        </tr>
        <tr>
            <td style="font-size: 14px">
                ردیف سفارش:
            </td>
            <td style="direction: ltr;font-size: 14px; font-weight:bold">

                @if($packing_form->items->first() && isset($packing_form->items->first()->production_form_item->production->order) && $packing_form->items->first()->production_form_item->production->order)

                    {{$packing_form->items->first()->production_form_item->production->order->getCodeByProductRow(
                            $packing_form->items->first()->product_id,
                            $packing_form->packing_type_id,

                        )
                        }}
                @endif
            </td>
        </tr>

        <tr>
            <td style="font-size: 14px">
                تاریخ تولید
            </td>
            <td style="font-size: 14px; font-weight:bold">

                {{$packing_form->get_create_date_and_time("Y/m/d")}}
            </td>
        </tr>


        <tr>
            <td style="font-size: 14px; width:25% ">
                کد رنگ
            </td>
            <td style="font-size: 14px; font-weight:bold;width:30%">
                {{$packing_form->items->first()->product->getPropertyValue(220360,"value",false,false)}}


            </td>

        </tr>

        <tr>
            <td style="font-size: 14px; width:25% ">
                جنس
            </td>
            <td style="font-size: 14px; font-weight:bold;width:30%">
                {{$packing_form->items->first()->product->getPropertyValue(220354,"caption",true,false)}}


            </td>

        </tr>
        <tr>
            <td style="font-size: 14px; ">
                رنگ
            </td>
            <td style="font-size: 14px; font-weight:bold;">

                {{$packing_form->items->first()->product->getPropertyValue(220321,"value",false,false)}}
            </td>
        </tr>
        <tr>
            <td style="font-size: 14px; ">
                نمره
            </td>
            <td style="font-size: 14px; font-weight:bold;">

                {{$packing_form->items->first()->product->getPropertyValue(220364,"value",false,false)}}
            </td>
        </tr>
        <tr>
            <td style="font-size: 14px; ">
                لات
            </td>
            <td style="font-size: 14px; font-weight:bold">

                {{$packing_form->items->first()->lot_number->code}}
            </td>

            <td style="font-size: 14px; ">
                درجه
            </td>
            <td style="font-size: 14px; font-weight:bold">

                {{$packing_form->items->first()->degree->caption}}
            </td>
        </tr>


        <tr>


            <td colspan="1" style="font-size: 15px">
                وزن خالص
            </td>
            <td colspan="3" style="font-size: 22px; color: #fff; background: #0b0b0b">

                @if(isset($unconfirmed_data))
                    {{$unconfirmed_data["final_amount"]}}
                @else
                    {{$packing_form->getFinalAmount()}}
                @endif

                &nbsp;
                {{$packing_form->getUnitCaption("unit","caption")}}
            </td>

        </tr>
        <tr>

            <td colspan="1" style="font-size: 15px">
                وزن ناخالص
            </td>
            <td colspan="3" style="font-size: 22px">


                {{$packing_form->gross_weight}}


                &nbsp;
                {{$packing_form->getUnitCaption("unit","caption")}}
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <div style="font-size: 18px">
                    @if(count($packing_form->packing_form_contents) > 0)
                        بسته بندی های فرعی از

                        {{$packing_form->packing_form_contents()->orderBy("id")->first()->code}}

                        تا
                        {{$packing_form->packing_form_contents()->orderByDesc("id")->first()->code}}
                    @else
                        شامل
                        @if(isset($unconfirmed_data))
                            {{$unconfirmed_data["sub_packing_form_number"]}}
                        @else
                            {{$packing_form->sub_packing_form_number}}
                        @endif
                        بسته بندی فرعی
                    @endif
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="4">
                <div style="font-size: 0.02px">
                    {!! $barcode_pin !!}
                </div>
            </td>
        </tr>

    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
