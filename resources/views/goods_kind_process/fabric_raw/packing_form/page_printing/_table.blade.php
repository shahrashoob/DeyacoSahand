<?php $sum_final_amount = 0; $sum_gross_weight = 0; ?>
<div class="table-responsive">
    <table class="table table-styling" style="text-align: center!important;">
        <thead>
        <tr>

            <th>ردیف</th>
            @if(isset($checkbox))
                <th><input type="checkbox" checked></th>
            @endif
            <th> کد بسته بندی</th>
            <th> شماره حامل</th>
            <th>کد کالا</th>
            <th>نام کالا</th>
            <th>مقدار کل</th>
            <th>وزن ناخالص
                <br/>
                (کیلوگرم)
            </th>
        </tr>

        </thead>
        <tbody>
        @php $row=$list->firstItem();@endphp
        @foreach($list as $item)
            <tr>
                <td>{{$row++}}</td>
                @if(isset($checkbox))
                    <td><input type="checkbox" name="packing_form_ids[{{$item->id}}]" checked class="check_box"></td>
                @endif
                <td>
                    {{$item->getCode()}}
                </td>
                <td>
                    {{$item->carrier?$item->carrier->getCaption():""}}
                </td>
                <td>
                        <?php $t = null; ?>
                    @foreach($item->items as $packing_form_item)
                        {{$t=$packing_form_item->product->code}}
                        @if(++$t>1)
                            <br/>
                        @endif
                    @endforeach
                </td>
                <td>
                        <?php $t = null; ?>
                    @foreach($item->items as $packing_form_item)
                        {{$t=$packing_form_item->product->caption}}
                        @if(++$t>1)
                            <br/>
                        @endif
                    @endforeach
                </td>
                <td>
                    {{$final_amount=$item->getAllAmount("final_amount")}}
                        <?php $sum_final_amount += $final_amount; ?>
                </td>
                <td>
                    {{$gross_weight=$item->gross_weight}}
                        <?php $sum_gross_weight += $gross_weight; ?>
                </td>


            </tr>
        @endforeach
        <tr>
            <td colspan="5">
                جمع کل
            </td>
            <td>
                {{$sum_final_amount}}
            </td>
            <td>
                {{$sum_gross_weight}}
            </td>
        </tr>
        </tbody>

    </table>
</div>