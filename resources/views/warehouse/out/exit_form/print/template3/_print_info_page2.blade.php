@php
    $itemOrderByTransportCode=$form->itemOrderByTransportCode("group_by_packing_form");
    $allow_show_sub_amount=$itemOrderByTransportCode[0]->packing_form_item->product->goods_kind->allow_show_sub_amount_in_exit_forms??1;

 @endphp
<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        <tr>
            <td colspan="9">
                برگ خروج کالا از انبار
                (به تفکیک بسته بندی)
            </td>
        </tr>

        <tr>
            <td colspan="9">
                @include("warehouse.out.exit_form.print.template3._info")
            </td>
        </tr>
        <tr>
            <th style="width: 60px">
                کد بسته بندی
            </th>
            <th>
                کد کالا
            </th>
            <th style="max-width: 35%">
                نام کالا
            </th>
            <th> {{$packing_form? $packing_form->getUnitCaption("unit","measurement"):"مقدار"}}
            </th>
            @php $sub_unit_measurement=$packing_form?$packing_form->getUnitCaption("sub_unit","measurement"):"";@endphp
            <th @if($sub_unit_measurement=="" || !$allow_show_sub_amount) style="width: 0px" @endif>
                @if($allow_show_sub_amount)
                    {{$sub_unit_measurement}}
                @endif
            </th>
            <th >درجه</th>
            <th>بسته بندی حمل و نقل</th>
            <th>تعداد آیتم</th>
            <th >توضیحات</th>
        </tr>

        {{--        به دست آوردن لیست به تفکیک بسته بندی ها--}}
        @php
            $row=0;
            $sum_item_count=0;
            $form=\App\Models\Form\Form::find($form->id);
        @endphp




        @foreach($itemOrderByTransportCode as $item)
            <tr>
                <td style="font-size: 14px">
                    {{$item->packing_form_item->packing_form->code}}
                </td>
                {{--                <td style="font-size: 14px">--}}
                {{--                    {{$item->product_request_form_item->product_request_form->code??""}}--}}
                {{--                </td>--}}
                <td style="font-size: 14px">
                    {{$item->packing_form_item->product->code}}
                </td>
                <td style="font-size: 14px">
                    {{$item->packing_form_item->product->caption}}
                </td>
                <td style="font-size: 14px">
                    {{$item->amount}}
                </td>
                <td style="font-size: 14px">
                    @if($sub_unit_measurement!=""  && $allow_show_sub_amount)
                        {{$item->sub_amount}}
                    @endif
                </td>
                <td>
                    {{$item->packing_form_item->degree->caption??""}}
                </td>
                <td>{{$item->packing_form_item->packing_form->getTransportPackingForm("transport_item_code")}}</td>
                <td>
                    {{$item["item_count"]}}
                </td>
                <td>

                </td>
            </tr>
            @php
                $row++;
                $sum_item_count+=$item["item_count"];
            @endphp
        @endforeach

        @foreach($form->getMasterPackingFrom() as $item)
            <tr>

                <td>  {{$item->code}}</td>
                <td>{{$item->items()->first()->product->code}}</td>
                <td>{{$item->items()->first()->product->caption}}</td>


                <td>{{$item->items()->first()->amount}}</td>
                <td>
                    @if($sub_unit_measurement!=""  && $allow_show_sub_amount)
                        {{$item->items()->first()->sub_amount}}
                    @endif

                </td>
                <td>  {{$item->items()->first()->degree->caption??""}}</td>
                <td></td>
                <td>

                </td>
                <td>
                    شامل
                    {{$item->packing_form_contents()->count()}}
                    بسته بندی فرعی
                </td>
            </tr>

        @endforeach

        @for($i=0;$i < 6-$row;$i++)
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
                <td>

                </td>
                <td>

                </td>
                <td>

                </td>

            </tr>

        @endfor
        <tr>
            <td colspan="3" style="border-bottom: none">
                جمع کل:
            </td>
            <td style="border-bottom: none">
                {{$sum_amount}}
            </td>
            <td style="border-bottom: none">
                @if($sub_unit_measurement!="" && $allow_show_sub_amount)
                    {{$sum_sub_amount}}
                @endif

            </td>
            <td style="border-bottom: none">

            </td>
            <td style="border-bottom: none">

            </td>
            <td style="border-bottom: none">
                {{$sum_item_count}}
            </td>
            <td style="border-bottom: none">

            </td>

        </tr>
        <tr>
            <td colspan="9" style="border-bottom: none; border-top: none">
                <table style="border: none">
                    <tr>
                        <td style="border-bottom: none; width:20%">
                            درخواست دهنده:

                        </td>
                        <td style="border-bottom: none; width:20%">
                            تحویل دهنده:

                        </td>
                        <td style="border-bottom: none; width:20%">
                            تایید کننده پیش نویس:

                        </td>
                        <td style="border-bottom: none; width:20%">
                            تایید کننده نهایی:

                        </td>
                        <td style="border-bottom: none; width:20%">
                            ارسال کننده:

                        </td>
                        <td style="border-bottom: none; width:20%">
                            تحویل گیرنده:


                        </td>
                        <td style="border-bottom: none; width:20%">
                            نگهبانی:


                        </td>
                    </tr>
                    @if(
                   (isset($product_request_form->order) && $product_request_form->order->selling_type_id==1 )||
                   !isset($product_request_form->order)
                   )
                        <tr>
                            <td style="border-top: none; font-size: 14px">


                                {{isset($product_request_form)?$product_request_form->applicant->fullCaption():""}}

                                <br/>
                                امضا
                            </td>
                            <td style="border-top: none; ">

                                @if(isset($product_request_form_logs[7005004]))
                                    {{$product_request_form_logs[7005004]->worker->fullname()}}
                                @endif
                                <br/>
                                امضا
                            </td>
                            <td style="border-top: none; font-size: 14px">

                                @if(isset($product_request_form_logs[7005010]))
                                    {{$product_request_form_logs[7005010]->worker->fullname()}}
                                @endif
                                <br/>
                                امضا

                            </td>
                            <td style="border-top: none; font-size: 14px">
                                @if(isset($product_request_form_logs[7005007]))
                                    {{$product_request_form_logs[7005007]->worker->fullname()}}
                                @endif
                                <br/>
                                امضا

                            </td>
                            <td style="border-top: none; font-size: 14px">

                                @if(isset($product_request_form_logs[7005014]))
                                    {{$product_request_form_logs[7005014]->worker->fullname()}}
                                @endif
                                <br/>
                                امضا

                            </td>
                            <td style="border-top: none; font-size: 14px">

                                @if(isset($product_request_form_logs[7005002]))
                                    {{$product_request_form_logs[7005002]->worker->fullname()}}
                                @endif
                                <br/>
                                امضا

                            </td>
                            <td style="border-top: none; font-size: 14px">

                                @if(isset($product_request_form_logs[7005009]))
                                    {{$product_request_form_logs[7005009]->worker->fullname()}}
                                @endif
                                <br/>
                                امضا

                            </td>
                        </tr>
                    @else
                        <tr>
                            <td style="border-top: none; font-size: 14px">


                                {{$product_request_form->applicant->fullCaption()}}
                                <br/>
                                امضا
                            </td>
                            @for($k=0;$k<6;$k++)

                                <td style="border-top: none; font-size: 14px">
                                    <br/>
                                    <br/>
                                    امضا

                                </td>

                            @endfor
                        </tr>
                    @endif
                </table>
            </td>


    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 9px">
    سازمان دیجیتال دیاکو
</div>
