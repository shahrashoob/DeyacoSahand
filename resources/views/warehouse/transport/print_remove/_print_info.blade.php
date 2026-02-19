<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <div style="width: 100%; height: 450px; border: 1px solid #000; font-size: 18px">
        <table style="width: 100%;">

            {{--        <tr>--}}
            {{--            <td colspan="6">--}}
            {{--                {{$header_text}}--}}
            {{--            </td>--}}
            {{--        </tr>--}}
            @if($transport_item->transport_packing_list()->first())
                <tr>
                    <td colspan="6">
                        <table style="border: none">
                            <tr>
                                <td style="border: none; text-align: center">
                                    <div style="font-size: .01px; position: fixed; left:0;padding: 3px ">
                                        {{$qr}}
                                    </div>
                                </td>
                                <td style="border: none; text-align: right;padding-right: 5px; font-size: 18px">

                                    شماره بار: {{$transport_item->transport->getCode()}}
                                    <br/>
                                    شماره بسته بندی: {{$transport_item->code()}}
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

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th  style="font-size: 18px">ردیف</th>
                    <th style="font-size: 18px">
                        کد بسته بندی
                    </th>
                    <th style="font-size: 18px">
                        کد طرح
                    </th>
                    <th style="font-size: 18px">
                        کد رنگ
                    </th>
                    <th style="font-size: 18px">
                        {{$transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("unit","measurement")}}
                    </th>
                    <th style="font-size: 18px">
                        درجه
                    </th>


                </tr>
            @endif
            @php $row=1;@endphp
            @foreach($transport_item->transport_packing_list as $item)
                <tr >
                    <td style="font-size: 18px">{{$row++}}</td>
                    <td style="font-size: 18px">
                        {{$item->packing_form->code}}
                    </td>
                    <td style="font-size: 18px">
                        {{$item->packing_form->items()->first()->product->getPropertyValue(220337,"value",true,false)}}
                    </td>
                    <td style="font-size: 18px">
                        {{$item->packing_form->items()->first()->product->getPropertyValue(220338,"value",true,false)}}
                    </td>
                    <td style="font-size: 18px">
                        {{$item->packing_form->getFinalAmount()}}
                    </td>
                    <td style="font-size: 18px">
                        {{$item->packing_form->items()->first()->degree->caption}}
                    </td>


                </tr>
            @endforeach

            {{--        <tr>--}}

            {{--            <td colspan="6" style="font-size: 38px">--}}

            {{--                {{$transport_item->transport->customer_caption??""}}--}}
            {{--            </td>--}}
            {{--        </tr>--}}

        </table>
    </div>
    <table style="width: 100%">
        <tr>
            <td colspan="6" style="padding-top: 5px">

                <div style="font-size: 0.02px; padding-bottom: 3px">
                    {!! $barcode !!}
                </div>
            </td>

        </tr>

    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
