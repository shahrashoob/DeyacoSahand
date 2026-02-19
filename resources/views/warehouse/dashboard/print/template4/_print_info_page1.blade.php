@php
    $getLowerPackingFormItem=$form->getLowerPackingFormItem();
    $allow_show_sub_amount=$getLowerPackingFormItem[0]->packing_form_item->product->goods_kind->allow_show_sub_amount_in_exit_forms??0;

@endphp
<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        <tr>
            <td colspan="6">
                {{$software_name}}

                <br/>
                فرم ورود به انبار
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
                        <td style="border: none; text-align: right;padding-right: 5px; font-size: 14px">
                            شماره فرم: {{$form->code}}
                            <br/>
                            تاریخ: {{$form->get_create_date_and_time()}}


                            <br/>
                            انبار:
                            {{$form->warehouse->caption}}

                            <br/>
                            نوع رخداد:
                            {{$form->trans_kind_item->caption}}
                            <br/>
                            مرکز هزینه:
                            {{$form->ic}}
                            <br/>
                            نام مرکز هزینه:
                            {{$form->cost_center_caption()}}
                            <br/>
                            شماره مرجع:
                            {{$form->referenceForInputForm()}}

                            <br/>
                            {{$packing_form? $packing_form->getUnitCaption("unit","measurement","کل:"):"مقدار"}}


                            {{$sum_amount}}

                            {{$packing_form? $packing_form->getUnitCaption("unit","caption"):"مقدار"}}

                            @if($packing_form && $packing_form->getUnitCaption("sub_unit","measurement","کل:"))
                                <br/>
                                {{$packing_form->getUnitCaption("sub_unit","measurement","کل:")}}

                                {{$sum_sub_amount}}
                                {{$packing_form->getUnitCaption("sub_unit","caption")}}
                            @endif

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th style="width: 60px">
                کد آیتم بسته بندی
            </th>
            {{--            <th>فرم درخواست</th>--}}
            <th>
                کد کالا
            </th>
            <th> {{$packing_form? $packing_form->getUnitCaption("unit","measurement"):"مقدار"}}
            </th>
            @php $sub_unit_measurement=$packing_form?$packing_form->getUnitCaption("sub_unit","measurement"):"";@endphp
            <th @if($sub_unit_measurement=="" || !$allow_show_sub_amount) style="width: 0px" @endif>

                @if($allow_show_sub_amount)
                    {{$sub_unit_measurement}}
                @endif

            </th>
            <th>درجه</th>
            <th>توضیحات</th>
        </tr>
        @php $row=0;@endphp
        @foreach($form->getLowerPackingFormItem() as $item)
            <tr>
                <td style="font-size: 14px">
                    {{$item->packing_form_item->code}}
                </td>
                {{--                <td style="font-size: 14px">--}}
                {{--                    {{$item->product_request_form_item->product_request_form->code??""}}--}}
                {{--                </td>--}}
                <td style="font-size: 14px">
                    {{$item->packing_form_item->product->code}}
                </td>
                <td style="font-size: 14px">
                    {{$item->amount}}
                </td>
                <td style="font-size: 14px">
                    @if($sub_unit_measurement!="" && $allow_show_sub_amount)
                        {{$item->sub_amount}}
                    @endif
                </td>
                <td>
                    {{$item->packing_form_item->degree->caption??""}}
                </td>
                <td>
                    {{$item->product_request_form_item->production->serial??""}}
                </td>
            </tr>
            @php $row++;
            @endphp
        @endforeach
        @foreach($form->getMasterPackingFrom() as $item)
            <tr>
                <td style="font-size: 14px">
                    {{$item->code}}
                </td>
                <td style="font-size: 14px">
                    {{$item->items()->first()->product->code}}
                </td>
                <td style="font-size: 14px">
                    {{$item->items()->first()->amount}}
                </td>
                <td style="font-size: 14px">
                    @if($sub_unit_measurement!="" && $allow_show_sub_amount)
                        {{$item->items()->first()->sub_amount}}
                    @endif

                </td>
                <td>
                    {{$item->items()->first()->degree->caption??""}}
                </td>
                <td>

                </td>
            </tr>
            @php $row++;
            @endphp
        @endforeach

        @for($i=0;$i < 7-$row;$i++)
            <tr>
                <td>

                </td>
                <td>

                </td>
                <td>
                    <br/>
                </td>
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>

            </tr>

        @endfor
        <tr>
            <td colspan="6" style="border-bottom: none">
                <div style="min-width: 50%">
                    ایجاد کننده:
                    <br/>
                    {{$form->worker->fullname()}}
                    <br/>
                    امضا

                </div>
                <div style="min-width: 50%">
                    تایید کننده:

                    @if(count($confirm_user_logs)>0)
                        @foreach($confirm_user_logs as $form_log)
                            <br/>
                            {{$form_log->worker->fullname()}}
                        @endforeach
                    @endif
                </div>
            </td>

        </tr>

    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 9px">
    سازمان دیجیتال دیاکو
</div>
