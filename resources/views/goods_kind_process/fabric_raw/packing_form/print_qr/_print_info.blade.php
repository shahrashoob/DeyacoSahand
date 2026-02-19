@if($packing_form->sub_packing()->count() > 0)
    <div class="content" style="font-size: 10px">
        <div style="text-align: center"></div>

        <div style="text-align: center">

        </div>
        <table style="width: 100%;">
            <tr>
                <td colspan="6">
                    {{$software_name}}
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <table style="border: none">
                        <tr>
                            <td style="border: none; text-align: center">
                                <div style="font-size: .01px; position: fixed; left:0 ">
                                    {{$qr}}
                                </div>
                            </td>
                            <td style="border: none; text-align: right;padding-right: 5px;font-size: 12px">
                                شماره فرم بسته بندی: {{$packing_form->code}}
                                <br/>
                                تاریخ بسته بندی: {{$packing_form->get_create_date_and_time()}}

                                <br/>
                                نوع بسته بندی: {{$packing_form->packing_type->caption}}
                                <br/>
                                تعداد و نوع بسته بندی فرعی:

                                {{$packing_form->sub_packing()->count()}}

                                {{$packing_form->sub_packing()->first()->packing_form->packing_type->caption}}

                                    <br/>
                                    {{$packing_form->getUnitCaption("unit","measurement","کل:")}}

                                    {{$packing_form->getFinalAmount()}}

                                    {{$packing_form->getUnitCaption("unit","caption")}}
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;
                                @if($packing_form->getUnitCaption("sub_unit","measurement","کل:"))

                                    {{$packing_form->getUnitCaption("sub_unit","measurement","کل:")}}

                                    {{$packing_form->getSubAmount()}}
                                    {{$packing_form->getUnitCaption("sub_unit","caption")}}

                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <th>
                    کد بسته بندی
                </th>
                <th style="width: 30px">
                    کد کالا
                </th>
                <th>
                    نام کالا
                </th>
                <th>
                    {{$packing_form->getUnitCaption("unit","measurement")}}
                </th>
                <th>تعداد اقلام</th>
                <th>
                    لات
                </th>


            </tr>

            @foreach($packing_form->sub_packing as $item)
                <tr>
                    <td style="width: 30px;font-size: 13px">
                        {{$item->packing_form->code}}
                    </td>
                    <td style="width: 30px;font-size: 13px">
                        {{$item->packing_form->items()->first()->product->code??"***"}}
                    </td>
                    <td style="font-size: 12px">
                        {{$item->packing_form->items()->first()->product->caption??"***"}}
                    </td>
                    <td style="font-size: 12px">
                        {{$item->packing_form->getAllAmount("final_amount")}}
                    </td>
                    <td style="font-size: 12px">
                        {{$item->packing_form->items()->count()}}
                    </td>
                    <td style="font-size: 12px">
                        {{$item->packing_form->items()->first()->lot_number->code??"***"}}

                    </td>


                </tr>

            @endforeach


        </table>
    </div>
    <div style="text-align: center; width: 100%;font-size: 11px">
        سازمان دیجیتال دیاکو
    </div>
@else
    <div class="content">
        <div style="text-align: center"></div>

        <div style="text-align: center">

        </div>
        <table style="width: 100%">
            <tr>
                <td colspan="4">
                    {{$software_name}}
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <table style="border: none">
                        <tr>
                            <td style="border: none; text-align: center">
                                <div style="font-size: .01px; position: fixed; left:0 ">
                                    {{$qr}}
                                </div>
                            </td>
                            <td style="border: none; text-align: right;padding-right: 5px; font-size: 13px">
                                شماره فرم بسته بندی: {{$packing_form->code}}
                                <br/>
                                تاریخ بسته بندی: {{$packing_form->get_create_date_and_time()}}

                                <br/>
                                نوع بسته بندی: {{$packing_form->packing_type->caption}}

                                <br/>
                                {{$packing_form->getUnitCaption("unit","measurement","کل:")}}

                                {{$packing_form->getFinalAmount()}}

                                {{$packing_form->getUnitCaption("unit","caption")}}

                                @if($packing_form->getUnitCaption("sub_unit","measurement","کل:"))
                                    <br/>
                                    {{$packing_form->getUnitCaption("sub_unit","measurement","کل:")}}

                                    {{$packing_form->getSubAmount()}}
                                    {{$packing_form->getUnitCaption("sub_unit","caption")}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <th style="width: 30px">
                    کد کالا
                </th>
                <th>
                    نام کالا
                </th>
                <th> {{$packing_form->getUnitCaption("unit","measurement")}}
                </th>
                <th>
                    لات
                </th>


            </tr>

            @foreach($packing_form->items as $item)
                <tr>
                    <td style="width: 30px;font-size: 14px">
                        {{$item->product->code}}
                    </td>
                    <td style="font-size: 14px">
                        {{$item->product->caption??"***"}}
                    </td>
                    <td style="font-size: 14px">
                        {{$item->final_amount}}
                    </td>
                    <td style="font-size: 14px">
                        {{$item->lot_number->code??"***"}}
                    </td>


                </tr>
            @endforeach


        </table>
    </div>
    <div style="text-align: center; width: 100%;font-size: 11px">
        سازمان دیجیتال دیاکو
    </div>
@endif
