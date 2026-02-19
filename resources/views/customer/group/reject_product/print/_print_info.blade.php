<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        <tr>
            <td colspan="7">
                فرم مرجوعی
            </td>
        </tr>

        <tr>
            <td colspan="7">
                @include("customer.group.reject_product.print._info")
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
            <th> {{$reject_product_form->items()->first()->packing_form->getUnitCaption("unit","measurement")}}
            </th>
            <th>
                {{$reject_product_form->items()->first()->packing_form->getUnitCaption("sub_unit","measurement")}}
            </th>
            <th>تعداد آیتم</th>
            <th colspan>توضیحات</th>
        </tr>

        {{--        به دست آوردن لیست به تفکیک بسته بندی ها--}}
        @php
            $row=0;
            $sum_amount=0;
            $sum_sub_amount=0;
        @endphp


        @foreach($reject_product_form->items as $reject_product_form_item)

            @php
                $packing_form_items=$reject_product_form_item->packing_form->items();
                $packing_form_item=$packing_form_items->first();
				$amount=$reject_product_form_item->packing_form->getFinalAmount();
				$sub_amount=$reject_product_form_item->packing_form->getSubAmount();
				$sum_amount+=$amount;
				$sum_sub_amount+=$sub_amount;
            @endphp
            <tr>
                <td style="font-size: 14px">
                    {{$reject_product_form_item->packing_form->code}}
                </td>
                <td style="font-size: 14px">
                    {{$packing_form_item->product->code}}
                </td>
                <td style="font-size: 14px">
                    {{$packing_form_item->product->caption}}
                </td>
                <td style="font-size: 14px">
                    {{$amount}}
                </td>
                <td style="font-size: 14px">
                    {{$sub_amount}}
                </td>
                <td style="font-size: 14px">
                    {{$packing_form_items->count()}}
                </td>
                <td>

                </td>
            </tr>
            @php
                $row++;
            @endphp
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
                {{$sum_sub_amount}}
            </td>

            <td style="border-bottom: none">

            </td>
            <td style="border-bottom: none">

            </td>

        </tr>
        <tr>
            <td colspan="7" style="border-bottom: none; border-top: none">
                <table style="border: none">
                    <tr>
                        <td style="border-bottom: none; width:20%">
                            درخواست دهنده:

                            {{$reject_product_form->applicant->fullCaption()}}
                        </td>
                        <td style="border-bottom: none; width:20%">
                            تایید کننده کنترل کیفیت:

                        </td>

                        <td style="border-bottom: none; width:20%">
                            نگهبانی:


                        </td>
                        <td style="border-bottom: none; width:20%">
                            انبار:

                        </td>
                    </tr>

                    <tr>
                        @for($k=0;$k<4;$k++)

                            <td style="border-top: none; font-size: 14px">
                                <br/>
                                <br/>
                                امضا

                            </td>

                        @endfor
                    </tr>

                </table>
            </td>


    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 9px">
    سازمان دیجیتال دیاکو
</div>
