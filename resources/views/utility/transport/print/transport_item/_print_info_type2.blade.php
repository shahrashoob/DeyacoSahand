<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <div style="width: 100%; height: 450px; border: 1px solid #000; font-size: 18px">
        <table style="width: 100%;border:none">

            {{--        <tr>--}}
            {{--            <td colspan="6">--}}
            {{--                {{$header_text}}--}}
            {{--            </td>--}}
            {{--        </tr>--}}
            @if($transport_item->transport_packing_list()->first())
                <tr>
                    <td style="border-left: none; border-bottom:none;text-align: center">
                        <div style="font-size: .01px; position: fixed; left:0;padding: 3px ">
                            {{$qr}}
                        </div>
                    </td>
                    <td colspan="5" style="border-right: none; border-bottom: none">
                        <table style="border: none">
                            <tr>

                                <td style="border: none; text-align: right;padding-right: 5px; font-size: 13px;border-bottom: none">
                                    @if($transport_item->transport)
                                        شماره بار: {{$transport_item->transport->getCode()}}
                                    @else
                                        شماره درخواست: {{$transport_item->product_request_form->getCode()}}
                                    @endif
                                    <br/>
                                    ش بسته بندی ح ن: {{$transport_item->code()}}
                                    <br/>

                                    {{$transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("unit","measurement","کل:")}}

                                    {{$transport_item->getAmount("final_amount")}}

                                    {{$transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("unit","caption")}}

                                    @if($transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("sub_unit","measurement","کل:"))
                                        <br/>
                                        {{$transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("sub_unit","measurement","کل:")}}

                                        {{$transport_item->getAmount("sub_amount")}}
                                        {{$transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("sub_unit","caption")}}
                                    @endif
                                    <br/>
                                    تاریخ:
                                    {{$date_time}}
                                    <br/>
                                    محتوا: شامل
                                    {{$transport_item->transport_packing_list()->count()}}
                                    بسته بندی

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            @endif
           <tr>
               <td colspan="6" style="border-top: none">
                   <div style="font-size: 0.02px; padding: 3px">
                       {!! $barcode !!}
                   </div>
                   <div style="text-align: center; width: 100%;font-size: 11px">
                       سازمان دیجیتال دیاکو
                   </div>
               </td>
           </tr>

        </table>
    </div>
</div>

