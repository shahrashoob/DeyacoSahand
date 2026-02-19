`<div class="content" style=" text-align: center; ">
    <img style="margin: 0px; " src="{{public_path('../storage/app/customer_image/parstak_poshesh.png')}}" >

    <table style="margin-top: -25px">
        <tr>
            <td colspan="2" class="center" style="border: none; text-align: center">



            </td>
        </tr>
        <tr>
            <td style="border: none">
                <table style=" width: 100%; margin: 0px">
                    <tr>
                        <td colspan="2" style="border: none; text-align: center;font-family: iransanse;font-weight: bolder; font-size: 20px" >


                            {{($packing_form->items->first()->product->getPropertyValue(220551,"value",true,false))}}

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"  style=" text-align: left;font-size: 15px;  border: 4px solid;padding: 1px">


                           <div style="font-size: 20px; font-family: calibri">
                               Code:
                               {{$packing_form->items->first()->getCustomerCodeFromTariff()}}
                           </div>


                        </td>
                    </tr>
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
                            Product Date
                        </td>

                        <td style="direction: ltr;border: none; " class="txt1">
                            {{$packing_form->get_create_date_en()}}
                        </td>
                    </tr>
                    <tr>
                        <td class="txt1" style="border: none; ">

                            Expire Date
                        </td>

                        <td style="border: none; " class="txt1">
                            {{$packing_form->get_create_date_en(365)}}

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="txt1" style="border: none; ">
                            Curing Condition
                       &nbsp;
                       &nbsp;
                       &nbsp;
                       &nbsp;
                            13'@ 200&#176;C


                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"  style="border: none; text-align: center; ">
                            <br/>
                            <div style="font-size: 0.02px">
                                {!! $barcode_pin !!}
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
            <td style="border: none">
                <table>

                    <tr>

                           <td style="border: none">

                               <table>
                                   <tr>
                                       <td style="border: none">
                                           {{$packing_form->getCode()}}
                                       </td>
                                       <td style="border: none"></td>
                                   </tr>
                                   <tr>
                                       <td style="border: none">
                                           Net:
                                       </td>
                                       <td style="border: none"></td>
                                   </tr>
                                   <tr>
                                       <td class="iransance" style="font-size: 35px;border: none" class="txt1">{{$packing_form->getFinalAmount()}}Kg</td>
                                       <td style="border: none; font-size: 16px">&#177;1%</td>
                                   </tr>
                               </table>

                        </td>
                    </tr>
                    <tr>

                           <td  style="border: none; ">
                               <img style="margin:0px; width: 100px " src="{{public_path('../storage/app/customer_image/parstak_poshesh_qr.png')}}" >
                        </td>
                    </tr>
                    <tr>

                           <td style="border: none; font-size: 14px">
                               Tel:
                          <div style="padding-left: 10px; font-size: 14px;margin: 0px">
                              021-22379355
                              <br/>
                              0938 519 2497
                          </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


    </table>



</div>

