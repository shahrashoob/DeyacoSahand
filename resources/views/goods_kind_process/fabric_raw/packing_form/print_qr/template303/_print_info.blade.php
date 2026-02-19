`<div class="content" style="padding: 10px; text-align: center">




            <table style=" width: 100%; margin: 0px">
                <tr>
                    <td colspan="2" class="txt1" style="border: none; text-align: right">

                            فام/رال:
                            {{($packing_form->items->first()->product->getPropertyValue(220551,"value",true,false))}}

                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="txt1" style="border: none; text-align: right">
                        کد محصول:
                        {{$packing_form->items->first()->getCustomerCodeFromTariff()}}


                    </td>
                </tr>
                <tr>
                    <td class="txt2" style="border: none; text-align: right">

                        سیستم:
                    </td>
                    <td class="txt2" style="border: none; text-align: right">
                        {{($packing_form->items->first()->product->getPropertyValue(220533,"caption",true,false))}}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; text-align: right" class="txt2">
                        شماره تولید:
                    </td>

                    <td style="direction: ltr;border: none;text-align: right " class="txt2">
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
                    </td>

                    <td style="border: none;text-align: right " class="txt2">
                        {{to_persian($packing_form->get_create_date_and_time())}}

                    </td>
                </tr>
                <tr>
                    <td class="txt2" style="border: none; text-align: right">
                        دمای پخت:
                    </td>

                    <td style="direction: rtl;border: none;text-align: right ;" class="txt2">
                        {{to_persian("200 درجه سانتیگراد / 15 دقیقه")}}


                    </td>
                </tr>
                <tr>
                    <td class="txt2" style="border: none; text-align: right">


                        وزن خالص:
                    </td>

                    <td style="border: none;text-align: right " class="txt2">

                        {{to_persian($packing_form->getFinalAmount())}}

                        {{$packing_form->getUnitCaption("unit","caption")}}

                    </td>
                </tr>
                <tr>
                    <td class="txt2" style="border: none; text-align: right; ">

                        دفتر فروش:


                    </td>
                    <td class="txt2" style="border: none; text-align: right;direction: ltr">

                        @php
                            $tell="";
                                $customer=$packing_form->items->first()->production_form_item->production->order->customer??null;
                                if($customer){
                                    $address=$customer->getDefaultAddress();
                                    $tell=$address->phone??"";
                                }
                        @endphp

                        {{to_persian($tell)}}


                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="txt2" style="border: none; text-align: center">
                        <div style="font-size: 10px">{{$packing_form->code}}</div>
                        <div style="font-size: 0.02px">
                            {!! $barcode_pin !!}
                        </div>
                    </td>
                </tr>
            </table>







</div>
