@php
    $itemOrderByTransportCode=$form->itemOrderByTransportCode("group_by_packing_form_item_parent_product");
    $first = reset($itemOrderByTransportCode);
    $goods_kind=$first->packing_form_item->product->goods_kind;

    $allow_show_sub_amount=$goods_kind->allow_show_sub_amount_in_exit_forms??1;

@endphp
<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        <tr>
            <td colspan="15">
                برگ خروج کالا از انبار
                (به تفکیک کالا)
            </td>
        </tr>

        <tr>
            <td colspan="15">
                @include("warehouse.out.exit_form.print.template3._info")
            </td>
        </tr>
        <tr>
            <th>
                کد طرح
            </th>
            <th>
                نام پارچه
            </th>

            <th>
                رنگ
            </th>
            <th>تعداد رول</th>
            <th>
                متراژ
            </th>
            <th>وزن خام</th>
            <th>
                عرض خام
                <br/>
                (سانتیمتر)
            </th>
            <th>نوع نخ</th>
            <th>
                نوع تکمیل
            </th>
            <th>
                کد رنگ
            </th>
            <th>عرض تکمیلی
                <br/>
                (سانتیمتر)
            </th>
            <th>
                کارت پیمان
            </th>
            <th>
                نوع بسته بندی
            </th>
            <th>
                متراژ / رول
            </th>
            <th>
                طول قواره
            </th>

        </tr>

        {{--        به دست آوردن لیست به تفکیک بسته بندی ها--}}
        @php
            $row=0;
            $form=\App\Models\Form\Form::find($form->id);
        @endphp


        @foreach($itemOrderByTransportCode as $item)
            <tr>
                <td style="font-size: 14px">
                    {{isset($item->parent_product)?$item->parent_product->getPropertyValue(220337,"value"):"---"}}
                </td>

                <td>
                    {{isset($item->parent_product)?$item->parent_product->getPropertyValue(220555,"value"):"---"}}

                </td>
                <td style="font-size: 14px">
                    {{isset($item->parent_product)?$item->parent_product->getPropertyValue(220338,"value"):"---"}}
                </td>
                <td>
                    {{$item->item_count}}
                </td>
                <td style="font-size: 14px">
                    {{$item->amount}}
                </td>
                <td style="font-size: 14px">
                    {{$item->sub_amount}}
                </td>
                <td style="font-size: 14px;  ">
                    {{$item->product->getPropertyValue(220263,"value") }}
                </td>
                <td>
                    {!! $item->yarn_types !!}
                </td>
                <td>
                    @php
                        $line_product_station=isset($item->parent_product)?$item->parent_product->line_product_station()->first():null;
                        $contractor_operation=$line_product_station->contractor_operation->caption??""
                    @endphp
                    {{$contractor_operation}}
                </td>
                <td style="font-size: 14px">
                    {{isset($item->parent_product)?$item->parent_product->getPropertyValue(220336,"value"):"---"}}

                </td>
                <td style="font-size: 14px">
                    {{isset($item->parent_product)?$item->parent_product->getPropertyValue(220248,"value"):"---"}}

                </td>


                <td style="font-size: 14px">
                    @foreach($item->parent_production_list as $pp_serail)
                        {{$pp_serail}}

                    @endforeach


                </td>

                <td style="font-size: 14px">
                    {{$item->packing_type_caption}}
                </td>

                <td>
                    درز به درز
                </td>
                <td>
                    {{$item->parent_product && $item->parent_product->sub_unit2_id?$item->parent_product->frame_ratio_unit2:""}}
                </td>

            </tr>
            @php
                $row++;
            @endphp
        @endforeach

        @for($i=0;$i < 3-$row;$i++)
            <tr>
                @for($j=0;$j <14;$j++)
                    <td><br/></td>
                @endfor

            </tr>

        @endfor
        <tr>
            <td colspan="3" style="border-bottom: none">
                جمع کل:
            </td>
            <td style="border-bottom: none">
                {{count($form->getPackingFormList())}}
            </td>
            <td style="border-bottom: none">
                {{$sum_amount}}
            </td>
            <td style="border-bottom: none">
                {{$sum_sub_amount}}
            </td>


            <td colspan="9" style="border-bottom: none">

            </td>

        </tr>
        <tr>
            <td colspan="15" style="border-bottom: none; border-top: none">
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
