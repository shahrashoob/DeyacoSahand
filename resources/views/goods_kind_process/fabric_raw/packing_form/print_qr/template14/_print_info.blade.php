<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        @if($packing_form->packing_type->label_caption!="")
            <tr>
                <td colspan="3" style="font-size: 17px; padding-top: 3px">
                    {{$packing_form->packing_type->label_caption=="system"?$software_name:$packing_form->packing_type->label_caption}}
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="3" style=" text-align: center; font-size: 17px;padding-top: 3px;padding-bottom: 6px">
                @foreach($packing_form->items as $item)
                    {{$item->product->caption??"***"}}
                @endforeach
            </td>
        </tr>

        <tr>
            <td style="font-size: 12px">
                شماره بسته بندی
            </td>
            <td style="font-size: 12px">

                {{$packing_form->getCode()}}
            </td>
            <td rowspan="6" style="border: none; text-align: center;padding-right: 5px; font-size: 13px;width: 45%">


                    شماره های مختلف با یکدیگر مخلوط نگردد.

                <br/>
              <div>
                  {{$packing_form->items->first()->lot_number->code}}
              </div>


                <br/>

                <div style="font-size: .01px; min-width: 30% ; padding-right: 15px">
                    {{$qr}}
                </div>


            </td>

        </tr>

        <tr>
            <td style="font-size: 13px; width:25% ">
                پایه رنگ
            </td>
            <td style="font-size: 13px;width:30%">
                {{$packing_form->items->first()->product->getPropertyValue(220533,"caption",true,false)}}


            </td>

        </tr>
        <tr>
            <td style="font-size: 13px; ">
                براقیت
            </td>
            <td style="font-size: 13px">

                {{$packing_form->items->first()->product->getPropertyValue(220548,"caption",true,false)}}
            </td>
        </tr>
        <tr>
            <td style="font-size: 13px">
                سریال تولید
            </td>
            <td style="direction: ltr;font-size: 13px">

                @if($packing_form->items->first() && isset($packing_form->items->first()->production_form_item->production->order) && $packing_form->items->first()->production_form_item->production->order)
                    <span style="display: inline">
                         {{$packing_form->items->first()->production_form_item->production->order->code()}} -
                    </span>
                @endif
                <span style="display: inline">
                    {{isset($packing_form->items->first()->production_form_item->production_form)?$packing_form->items->first()->production_form_item->production_form->getCode():""}}
                      - {{$packing_form->items->first()->GetRowInProductionFormItem()}}            </td>
        </tr>

        <tr>
            <td style="font-size: 13px">
                تاریخ بسته بندی
            </td>
            <td style="font-size: 10px">

                {{$packing_form->get_create_date_and_time()}}
            </td>
        </tr>
        <tr>
            <td style="font-size: 13px">
                مقدار کل
            </td>
            <td style="font-size: 13px">

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
            <td colspan="3" style="font-size: 9px;padding: 3px">
                <div style="text-align: center; width: 100%;padding:5px;font-size: 0.02px">
                    {!! $barcode !!}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-size: 9px;padding: 3px">
                {!! $packing_form->items->first()->product->getPropertyValue(220556,"caption",true,false) !!}

{{--                شرایط پخت 15 دقیقه در دمای 180 درجه یا 10 دقیقه در دمای 200 درجه سانتیگراد--}}
{{--                <br/>--}}
{{--                بهترین زمان مصرف تا یکسال پس از تولید می باشد.--}}
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div style="font-size: 0.02px">
                    {!! $barcode_pin !!}
                </div>
            </td>
        </tr>

    </table>
</div>
