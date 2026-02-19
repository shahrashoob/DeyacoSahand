<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        @if($packing_form->packing_type->label_caption!="")
            <tr>
                <td colspan="5">
                    {{$packing_form->packing_type->label_caption=="system"?$software_name:$packing_form->packing_type->label_caption}}
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="5">
                <table style="border: none">
                    <tr>
                        <td style="border: none; text-align: right;padding-right: 5px; font-size: 13px;line-height: 1.8;">
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
                            @if($packing_form->carrier)
                                <br/>
                                شماره حامل:
                                {{$packing_form->carrier->code}}

                            @endif
                            <br/>
                            {{--                            {{$packing_form->getUnitCaption("unit","measurement","کل:")}}--}}

                        @if($packing_form->gross_weight)
                                وزن نا خالص (کیلوگرم):
                                @if(isset($unconfirmed_data))
                                    {{$unconfirmed_data["gross_weight"]}}
                                @else
                                    {{$packing_form->gross_weight}}
                                @endif

                        @endif

                        </td>
                        <td style="border: none; text-align: center">
                            <div style="font-size: .01px; position: fixed; left:0 ">
                                {{$qr}}
                            </div>
                        </td>

                    </tr>
                </table>
            </td>
        </tr>

<tr>
    <td colspan="3" style="font-size: 20px; ">
        {{$packing_form->code}}
    </td>
    <td  colspan="2" style="font-size: 40px;direction: ltr ">
        {{$packing_form->items->first()->final_amount}}
        @if($packing_form->items->first()->product->unit_id == 300)
            <span style="font-size: 15px"> Kg</span>
        @else
            {{$packing_form->items->first()->product->unit->caption}}
        @endif


    </td>
</tr>

<tr>
    <td style="width: 60px; height: 60px" >
نام کالا
    </td>
    <td colspan="4" style="font-size: 30px; ">
        {{$packing_form->items->first()->product->caption}}
    </td>
</tr>

<tr>
    <td >
کد کالا
    </td>
    <td colspan="4" style="font-size: 25px;">
        {{$packing_form->items->first()->product->code}}
    </td>
</tr>

<tr>
    <td >
درجه
    </td>
    <td  >
        {{$packing_form->items->first()->degree->caption}}
    </td>

    <td colspan="2">
        لات
    </td>
    <td  >
        {{$packing_form->items->first()->lot_number->code}}
    </td>
</tr>

<tr>

</tr>



        @if(isset($unconfirmed_data))
            <tr>
                <th colspan="5">
                    اطلاعات بسته بندی توسط انبار تایید نشده است.
                </th>


            </tr>
        @endif
    </table>
</div>
<div style="text-align: center; width: 100%;padding:5px;font-size: 0.02px">
    {!! $barcode !!}
</div>
<div style="text-align: center; width: 100%;padding:6px;font-size: 0.02px">
    {!! $barcode_pin !!}
</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
