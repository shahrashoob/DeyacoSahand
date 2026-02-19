<div class="content" style="font-size: 12px">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        <tr>
            <td colspan="6">
                برگ خروج کالا از انبار
                (به تفکیک کالا)
            </td>
        </tr>

        <tr>
            <td colspan="6">
                @include("warehouse.out.exit_form.print.template12._info")
            </td>
        </tr>
        <tr>
            <th>
                کد کالا
            </th>
            <th style="max-width: 35%">
                کالیته
            </th>
            <th style="max-width: 35%">
                رنگ
            </th>

            <th colspan>درجه</th>
            <th> {{$packing_form? $packing_form->getUnitCaption("unit","measurement"):"مقدار"}}
            </th>
            <th >توضیحات</th>
        </tr>

        {{--        به دست آوردن لیست به تفکیک کالا ها--}}
        @php
            $row=0;
            $form=\App\Models\Form\Form::find($form->id);
        @endphp


        @foreach($form->itemOrderByTransportCode("group_by_product") as $item)
            <tr>
                <td >
                    {{$item->packing_form_item->product->code??""}}
                </td>
                <td >
                    {{$item->packing_form_item->product->property1_caption??""}}
                </td>
                <td >
                    {{$item->packing_form_item->product->property2_caption??""}}
                </td>
                <td>
                    {{$item->packing_form_item->degree->caption??""}}
                </td>
                <td >
                    {{$item->amount}}
                </td>

                <td>
                    @if(isset($product_packing_form_count[$item->product_id."_".$item->degree_id]))
                        {{$product_packing_form_count[$item->product_id."_".$item->degree_id]["packing_form_count"]}}
                        بسته بندی
                    @endif
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

                </td>
                <td>
                    <br/>
                </td>

                <td>

                </td>
                <td>

                </td>

            </tr>

        @endfor
        <tr>
            <td colspan="4" >
                جمع کل:
            </td>
            <td >
                {{$sum_amount}}
            </td>
           <td></td>

        </tr>
        <tr>



    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 10px">
    سازمان دیجیتال دیاکو
</div>
