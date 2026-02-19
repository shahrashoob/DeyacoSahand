{{--پارچه تکمیل شده--}}
<tr>
    <td style="border: none;text-align: right;font-size: 11px;">
        کد طرح:
        {{$packing_form->items->first()->product->getPropertyValue(220337,"value",true,false)}}
    </td>
    <td style="border: none;font-size: 11px;text-align: right">
        کد رنگ:
        {{$packing_form->items->first()->product->getPropertyValue(220338,"value",true,false)}}
    </td>
</tr>