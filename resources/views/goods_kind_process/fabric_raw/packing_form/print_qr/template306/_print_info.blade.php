`<div class="content" style="padding: 10px; text-align: center">

@php
$customer_product=$packing_form->items->first()->getCustomerCodeFromTariff("code_caption");
$customer_product_code="";
$customer_product_caption="";
if(is_array($customer_product)){
    $customer_product_code=$customer_product["code"];
    $customer_product_caption=$customer_product["caption"];
}
@endphp


            <table style=" width: 100%; margin: 0px">
                <tr>
                    @if(strlen($customer_product_caption) < 20)
                    <td colspan="2"  style="border: none;font-size: 40px; text-align: center">
                        {{$customer_product_caption}}
                    </td>
                    @else
                        <td colspan="2"  style="border: none;font-size: 25px; text-align: center">
                            {{$customer_product_caption}}
                        </td>
                    @endif
                </tr>
                <tr>
                    <td colspan="2" class="txt2" style="border: none; text-align: right">

                            نوع:
                            {{($packing_form->items->first()->product->getPropertyValue(220533,"caption",true,false))}}

                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="txt2" style="border: none; text-align: right">
                        کد:
                        {{$customer_product_code}}


                    </td>
                </tr>
                <tr>
                    <td style="border: none; text-align: right" class="txt2">
                        شماره تولید:

                        @if($packing_form->items->first() && isset($packing_form->items->first()->production_form_item->production->order) && $packing_form->items->first()->production_form_item->production->order)
                            <span style="display: inline">
                         {{$packing_form->items->first()->production_form_item->production->order->code()}} -
                    </span>
                        @endif
                        <span style="display: inline">
                    {{isset($packing_form->items->first()->production_form_item->production_form)?$packing_form->items->first()->production_form_item->production_form->getCode():""}}
                      - {{$packing_form->items->first()->GetRowInProductionFormItem()}}
                    </td>
                </tr>
                <tr>
                    <td class="txt2" style="border: none; text-align: right">

                        تاریخ تولید:

                        {{to_persian($packing_form->get_create_date_and_time())}}

                    </td>
                </tr>
                <tr>
                    <td colspan="2"  style="border: none; font-size: 10px; text-align: right">
                     <div style="font-size: 11px">
                         شرایط پخت: دماي 200 درجه بمدت 10 دقيقه يا 180درجه بمدت 15 دقيقه
                     </div>
<br/>
                        توجه: در جاي خشك و خنك و دور از نور مستقيم آفتاب ( زير 25 درجه) نگهداري شود.

                    </td>
                </tr>
                <tr>
                    <td colspan="2"  style="border: none;font-size: 10px; text-align: right">



                    </td>
                </tr>
                <tr>
                    <td colspan="2"  style="border: none; text-align: center">
                        <div style="font-size: 9px">{{$packing_form->code}}</div>
                        <div style="font-size: 0.02px">
                            {!! $barcode_pin !!}
                        </div>
                    </td>
                </tr>
            </table>

</div>
