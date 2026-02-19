@php
    $itemOrderByTransportCode=$form->itemOrderByTransportCode("group_by_product");
    $allow_show_sub_amount=$itemOrderByTransportCode[0]->packing_form_item->product->goods_kind->allow_show_sub_amount_in_exit_forms??1;

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
            <th>ردیف</th>
            <th> کد کالا</th>
            <th> نام کالا</th>
            <th>    {{$packing_form? $packing_form->getUnitCaption("unit","measurement"):"مقدار"}}</th>
            @php $sub_unit_measurement=$packing_form?$packing_form->getUnitCaption("sub_unit","measurement"):"";@endphp
            <th @if($sub_unit_measurement=="" || !$allow_show_sub_amount) style="width: 0px" @endif>

                @if($allow_show_sub_amount)
                    {{$sub_unit_measurement}}
                @endif

            </th>
            <th>درجه</th>
            {{--                                <th> توضیحات</th>--}}
        </tr>


        @php
            $row=0;
            $form=\App\Models\Form\Form::find($form->id);
        @endphp
        @foreach($itemOrderByTransportCode as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>{{$item->product->code}}</td>
                <td>{{$item->product->caption}}</td>


                <td>{{$item->amount}}</td>

                <td>
                    @if($sub_unit_measurement!="" && $allow_show_sub_amount)
                        {{$item->sub_amount}}
                    @endif
                </td>

                <td>  {{$item->degree->caption??""}}</td>
                {{--                                    <td>{!! $item["description"] !!}</td>--}}
            </tr>

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
        <tr style="font-weight: bold;font-size: 16px">
            <td colspan="3">
                جمع کل
            </td>
            <td>
                {{$sum_amount}}
            </td>
            <td>
            @if($sub_unit_measurement!="" && $allow_show_sub_amount)

                    {{$sum_sub_amount}}

            @endif
            </td>
            <td>

            </td>
        </tr>



        <tr>
            <td colspan="6">
                <table >
                    <tr>
                        <td>
                            ایجاد کننده:
                            <br/>
                            {{$form->worker->fullname()}}
                            <br/>
                            امضا
                        </td>
                        <td>
                            تایید کننده:

                            @if(count($confirm_user_logs)>0)
                                @foreach($confirm_user_logs as $form_log)
                                    <br/>
                                    {{$form_log->worker->fullname()}}
                                @endforeach
                            @endif
                        </td>
                    </tr>
                </table>



            </td>

        </tr>
    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 9px">
    سازمان دیجیتال دیاکو
</div>
