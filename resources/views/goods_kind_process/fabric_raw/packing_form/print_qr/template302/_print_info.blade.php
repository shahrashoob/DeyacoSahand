<div class="content" style="padding: 10px; text-align: center">

    <table style="width: 100%; height: 600px">

        <tr>
            <td style="">
                نوع رنگ
            </td>
            <td style="">
                {{($packing_form->items->first()->product->getPropertyValue(220551,"value",true,false))}}
            </td>

        </tr>

        <tr>
            <td style=" width:40% ">
                کد
            </td>
            <td style="font-size: 20px;direction: ltr">
                {{$packing_form->items->first()->getCustomerCodeFromTariff()}}


            </td>

        </tr>
        <tr>
            <td>
                شماره تولید
            </td>
            <td style="direction: ltr; ">
                @if($packing_form->items->first() && isset($packing_form->items->first()->production_form_item->production->order) && $packing_form->items->first()->production_form_item->production->order)
                    <span style="display: inline">
                         {{$packing_form->items->first()->production_form_item->production->order->code()}} -
                    </span>
                @endif
                <span style="display: inline">
                    {{isset($packing_form->items->first()->production_form_item->production_form)?$packing_form->items->first()->production_form_item->production_form->getCode():""}}
                      -  {{$packing_form->items->first()->GetRowInProductionFormItem()}}
                </span>

            </td>
        </tr>
        <tr>
            <td>
                شماره بسته بندی
            </td>
            <td style="">

                {{$packing_form->getCode()}}
            </td>
        </tr>
        <tr>
            <td style="">
                شرایط پخت
            </td>
            <td style="">
                {{to_persian("200 درجه به مدت 10 دقیقه")}}

            </td>
        </tr>

        <tr>
            <td style="">
                کاربری
            </td>
            <td>

                سیستم کورونا
            </td>
        </tr>
        <tr>
            <td style="">
                وزن خالص
            </td>
            <td style="">

                {{to_persian($packing_form->getFinalAmount())}}

                {{$packing_form->getUnitCaption("unit","caption")}}
            </td>
        </tr>


    </table>
    <div style="font-size: 12px; padding: 4px">
        این محصول برای محیط در معرض نور خورشید توصیه نمی گردد.
    </div>
    <div style="font-size: 0.02px">
        {!! $barcode_pin !!}
    </div>
</div>
