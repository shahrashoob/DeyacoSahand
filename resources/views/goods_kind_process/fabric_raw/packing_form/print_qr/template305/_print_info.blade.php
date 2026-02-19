`<div class="content" style=" text-align: center; ">
    <img style="margin: 0px;margin-top: -10px " src="{{public_path('../storage/app/customer_image/leroux.png')}}" >

    <table style="margin-top: -3px; border-spacing: 0px">
        <tr>
            <td colspan="2" style="border: none">
                <table style=" width: 100%; margin: 0px; border-spacing: 0px">
                    <tr>


                        <td style=" text-align: left;font-size: 15px;  border: 2px solid;padding: 1px">


                            <div style="font-size: 13px; font-family: calibri">
                                Code:<br/>
                               <div style="font-size: 24px"> {{$packing_form->items->first()->getCustomerCodeFromTariff()}}</div>

                            </div>


                        </td>
                        <td  style="border: none; text-align: center;font-family: iransanse;font-weight: bolder; font-size: 16px; " >


                            {{($packing_form->items->first()->product->getPropertyValue(220551,"value",true,false))}}

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border: none">
                <table style=" width: 100%; margin: 0px">
                    <tr>
                        <td  style="border: none;" class="txt1">

                              Batch No.
                        </td>
                        <td style="border: none; width: 50%" class="txt1">
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
                        <td style="border: none;" class="txt1">
                            Mfg. Date
                        </td>

                        <td style="direction: ltr;border: none; " class="txt1">
                            {{$packing_form->get_create_date_en()}}
                        </td>
                    </tr>
                    <tr>
                        <td class="txt1" style="border: none; ">

                            Exp. Date
                        </td>

                        <td style="border: none; " class="txt1">
                            {{$packing_form->get_create_date_en(365)}}

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="txt1" style="border: none; ">
                            Curing Condition

                       &nbsp;
                            13'@ 200&#176;C


                        </td>
                    </tr>


                </table>
            </td>
            <td style="border: none;">
                <table style="margin: 0px">

                    <tr>

                           <td style="border: none">

                               <table>
                                   <tr>
                                       <td style="border: none;font-size: 13px;">
                                           {{$packing_form->getCode()}}
                                       </td>

                                   </tr>
                                   <tr>
                                       <td style="border: none;font-size: 16px;">
                                           Weight:
                                       </td>

                                   </tr>
                                   <tr>
                                       <td class="iransance" style="font-size: 16px;border: none" class="txt1">


                                           {{$packing_form->getFinalAmount()}}Kg &#177;1%
                                       </td>

                                   </tr>
                               </table>

                        </td>
                    </tr>


                </table>
            </td>
        </tr>
        <tr>
            <td style="border: none;  border-top: 1px solid">
                <table style="margin: 0px; ">
                    <tr>
                        <td  style="border: none; text-align: center; ">
                            <div style="font-size: 0.02px">
                                {!! $barcode_pin !!}
                            </div>
                        </td>
                    </tr>
                    <tr>

                        <td style="border: none; font-size: 12px; text-align: center">
                            Tel:
                            +98-9128353647
                            <div style="margin-left: 15px">
                               &nbsp;&nbsp; &nbsp;&nbsp;  &nbsp;  +98-2182809644
                                <br/>
                                www.lerouxCompany.com
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="text-align: center; vertical-align: middle;border: none;border-top: 1px solid; border-left: 1px solid"  >
                <img style="margin:0px; width: 90px " src="{{public_path('../storage/app/customer_image/leroux_qr.png')}}" >
            </td>
        </tr>


    </table>



</div>

