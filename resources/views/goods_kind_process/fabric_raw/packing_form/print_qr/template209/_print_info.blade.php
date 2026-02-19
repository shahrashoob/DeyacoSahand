<div class="content" style="padding: 2px; border: 1px solid; border-radius: 10px" >

                <div style=" text-align: center; width: 600px !important; border: 1px solid;border-radius: 10px;font-size:19px;font-family: 'arialroundedmtbold';!important; color: #fff;background: #000; word-spacing: 5px " >
                    &nbsp;

                    ELKAVA   TEXTILE   COMPANY

                    &nbsp;
                </div>

    <table style="width: 100%;text-align: left !important; ">
        <tr>

                <td colspan="3" style="font-size:15px; font-weight: bold; padding: 3px; border: none ">


                </td>


        </tr>


        <tr>
            <td colspan="2" style="border: none; padding-right: 3px;">
                <table style="border: none; margin-left: -10px">

                    <tr>
                        <td rowspan="7" style="border: none; width: 40px">
                            <table style="border: none">
                                <tr>

                                    <th style="border: none; text-align: left;direction: ltr; padding: 10px; font-size: 15px; font-family: 'arialroundedmtbold'" text-rotate="90">


                                         {{$packing_form->getSubAmount()}}K
                                    </th>
                                    <th style="border: none; font-size: 25px; font-family: 'arialroundedmtbold'" text-rotate="90">

                                        {{$packing_form->getFinalAmount()}}M

                                    </th>


                                </tr>
                            </table>

                        </td>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">




                            {{$packing_form->items->first()->product->getPropertyValue(220337,"value",true,false)}}



                        </td>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            :DESIGN NO
                        </td>

                        <td rowspan="6"  style=" border: none; ">


                        </td>



                    </tr>
                    <tr>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            {{$packing_form->items->first()->lot_number->code}}
                        </td>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            :LOT NO
                        </td>
                    </tr>
                    <tr>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            {{$packing_form->getCode()}}
                        </td>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            :PACKAGE NO

                        </td>
                    </tr>
                    <tr>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            {{$packing_form->items->first()->product->getPropertyValue(220338,"value",true,false)}}

                        </td>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            :COLOR CODE

                        </td>
                    </tr>
                    <tr>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            @if($packing_form->items->first() && isset($packing_form->items->first()->production_form_item->production->order) && $packing_form->items->first()->production_form_item->production->order)

                                {{$packing_form->items->first()->production_form_item->production->order->customer->code}}
                            @endif
{{--                            @if(isset($packing_form->items->first()->production_form_item->production->order->customer))--}}
{{--                                (--}}
{{--                                {{$packing_form->items->first()->production_form_item->production->order->customer->code??""}}--}}
{{--                                )--}}
{{--                            @endif--}}
                        </td>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            :CUSTOMER NO
                        </td>
                    </tr>
                    <tr>
                        <td  style="border: none;direction: ltr; text-align: left; font-size: 11px;padding-right: 3px;">
                            {{$packing_form->items->first()->degree->caption}}
                        </td>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            :GRADE
                        </td>
                    </tr>
                    <tr>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            {{$packing_form->items->first()->product->getPropertyValue(220248,"value",true,false)}}

                        </td>
                        <td  style="border: none; text-align: left; font-size: 11px;padding-right: 3px;">
                            :WIDTH
                        </td>
                    </tr>


                </table>
            </td>
            <td rowspan="2  " style="width: 50px; text-align: center; border: none; position: relative; padding-bottom: 8px; padding-left: 10px">
{{--                <img style="margin: 0px; width: 65px; padding-bottom: 3px " src="{{public_path('../storage/app/customer_image/elkava.png')}}" >--}}
                <img style="width: 75px; padding-bottom: 10px " src="{{public_path('../storage/app/customer_image/elkava.png')}}" >
<br/>
{{--                <div style="font-size: .01px; position: fixed; left:0; bottom: 0 ">--}}
{{--                    {{$qr}}--}}
{{--                </div>--}}

                <div style="font-size: .01px;  position: absolute; ">
                    {{$qr}}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border: none;">
                <div style="font-size: 0.02px">
                    {!! $barcode_pin !!}
                </div>
            </td>

        </tr>


    </table>
</div>

