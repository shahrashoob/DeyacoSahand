<div style="text-align: center; width: 100%;padding:6px;font-size: 0.02px">
    {!! $barcode_pin !!}
</div>
<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        @if($packing_form->packing_type->label_caption!="")
            <tr>
                <td colspan="4">
                    {{$packing_form->packing_type->label_caption=="system"?$software_name:$packing_form->packing_type->label_caption}}
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="4">
                <table style="border: none">
                    <tr>
                        <td style="border: none; text-align: center">
                            <div style="font-size: .01px; position: fixed; left:0 ">
                                {{$qr}}
                            </div>
                        </td>
                        <td style="border: none; text-align: right;padding-right: 5px; font-size: 13px;">
                            شماره بسته بندی: {{$packing_form->getCode()}}
                            <br/>
                            تاریخ بسته بندی: {{$packing_form->get_create_date_and_time()}}

                            <br/>
                            نوع بسته بندی: {{$packing_form->packing_type->code}}
                            - {{$packing_form->packing_type->caption}}

                            @if($packing_form->packing_form_contents()->count())
                                <br/>
                                تعداد  بسته بندی فرعی:

                                @if(isset($unconfirmed_data))
                                    {{$unconfirmed_data["sub_packing_form_number"]}}
                                @else
                                    {{$packing_form->packing_form_contents()->count()}}
                                @endif


                            @endif
                            <br/>
                            {{--                            {{$packing_form->getUnitCaption("unit","measurement","کل:")}}--}}

                            مقدار کل:
                            @if(isset($unconfirmed_data))
                                {{$unconfirmed_data["final_amount"]}}
                            @else
                                {{$packing_form->getFinalAmount()}}
                            @endif

                            {{$packing_form->getUnitCaption("unit","caption")}}

                            @if($packing_form->getUnitCaption("sub_unit","measurement","کل:"))
                                <br/>
                                {{$packing_form->getUnitCaption("sub_unit","measurement","کل:")}}

                                @if(isset($unconfirmed_data))
                                    {{isset($unconfirmed_data["sub_amount"])?$unconfirmed_data["sub_amount"]:""}}
                                @else
                                    {{$packing_form->getSubAmount()}}
                                @endif

                                {{$packing_form->getUnitCaption("sub_unit","caption")}}
                            @endif

                            @if($packing_form->weight)
                                <br/>
                                وزن خالص:
                                @if(isset($unconfirmed_data))
                                    {{$unconfirmed_data["weight"]}}
                                @else
                                    {{$packing_form->weight}}
                                @endif

                                کیلوگرم
                            @endif


                            @if($packing_form->gross_weight)
                                <br/>
                                وزن نا خالص:
                                @if(isset($unconfirmed_data))
                                    {{$unconfirmed_data["gross_weight"]}}
                                @else
                                    {{$packing_form->gross_weight}}
                                @endif
                                کیلوگرم
                            @endif


                        </td>
                    </tr>
                </table>
            </td>
        </tr>


        @if($packing_form->sub_packing_form_number==0)
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
                        {{round($item->final_amount,2)}}
                    </td>
                    <td style="font-size: 14px">
                        {{$item->lot_number->code??"***"}}
                    </td>


                </tr>
            @endforeach


        @elseif($packing_form->packing_type->max_row_to_display_sub_packing_in_print < $packing_form->sub_packing_form_number)
            @php $item_row=$packing_form->items()->first();@endphp
            <tr>
                <th style="width: 30px">
                    کد کالا
                </th>
                <th>
                    نام کالا
                </th>

                <th> درجه
                </th>
                <th>
                    لات
                </th>


            </tr>
            <tr>
                <th>
                    {{$item_row->product->code}}
                </th>
                <th>
                    {{$item_row->product->caption}}
                </th>

                <th>
                    {{$item_row->degree->caption??""}}
                </th>

                <th>
                    {{$item_row->lot_number->code??""}}
                </th>


            </tr>
            <tr>
                <th colspan="4">
                    @if(count($packing_form->packing_form_contents) > 0)
                        بسته بندی های فرعی از

                        {{$packing_form->packing_form_contents()->orderBy("id")->first()->code}}

                        تا
                        {{$packing_form->packing_form_contents()->orderByDesc("id")->first()->code}}
                    @else
                        شامل
                        @if(isset($unconfirmed_data))
                            {{$unconfirmed_data["sub_packing_form_number"]}}
                        @else
                            {{$packing_form->sub_packing_form_number}}
                        @endif
                        بسته بندی فرعی
                    @endif

                </th>
            </tr>
        @else
            <tr>
                <th style="width: 30px">
                    شماره فرم بسته بندی فرعی
                </th>
                <th>
                    نوع بسته بندی
                </th>
                <th> {{$packing_form->getUnitCaption("unit","measurement")}}
                </th>
                <th>
                    لات
                </th>


            </tr>
            @foreach($packing_form->packing_form_contents as $packing_form_content)
                @foreach($packing_form_content->items as $item)
                    <tr>
                        <td style="width: 30px;font-size: 14px">
                            {{$packing_form_content->code}}
                        </td>
                        <td style="font-size: 14px">
                            {{$packing_form_content->packing_type->caption??""}}
                        </td>
                        <td style="font-size: 14px">
                            {{round($item->final_amount,2)}}
                        </td>
                        <td style="font-size: 14px">
                            {{$item->lot_number->code??"***"}}
                        </td>


                    </tr>
                @endforeach

            @endforeach

        @endif

        @if(isset($unconfirmed_data))
            <tr>
                <th colspan="4" >
                   اطلاعات بسته بندی توسط انبار تایید نشده است.
                </th>


            </tr>
        @endif
    </table>
</div>
<div style="text-align: center; width: 100%;padding:5px;font-size: 0.02px">
    {!! $barcode !!}
</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
