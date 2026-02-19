@php               $row=1;
                           $sum_price=0;
                           $sum_amount=0;
                           $sum_total_price=0;
                           $sum_off_price=0;
                           $sum_tax_price=0;
                           $sum_total_price_with_tax=0;
@endphp
@foreach($form_factor as $item)
    @php
        $sum_amount+=$item["amount"];
        $sum_price+=$item["price"];
        $sum_total_price+=$item["total_price"];
        $sum_off_price+=$item["total_off_price"];
        $sum_tax_price+=$item["tax_price"];
        $sum_total_price_with_tax+=$item["total_price_with_tax"];
    @endphp
    <tr>
        <td>{{to_persian($row++)}}</td>
        <td>{{to_persian($item["product_service_code"])}}</td>

        @if($setting_data["show_product_caption_in_pre_factor"])
            <td>{{to_persian($item["product_service_caption"])}}</td>
        @endif


        @if($setting_data["show_packing_type_caption_in_pre_factor"] || $setting_data["show_packing_type_code_in_pre_factor"])

            <td>

                @if($setting_data["show_packing_type_caption_in_pre_factor"] && $setting_data["show_packing_type_code_in_pre_factor"])
                    {{to_persian($item["item"]->packing_type->caption)}}
                    -    {{to_persian($item["item"]->packing_type->code)}}
                @elseif($setting_data["show_packing_type_caption_in_pre_factor"])
                    {{to_persian($item["item"]->packing_type->caption)}}
                @elseif($setting_data["show_packing_type_code_in_pre_factor"])
                    {{to_persian($item["item"]->packing_type->code)}}
                @endif


            </td>
        @endif

        @if($setting_data["show_property_1_in_pre_factor"])
            <td>{{$item["item"]->product->property1_caption=="0"?"":$item["item"]->product->property1_caption}}</td>
        @endif

        @if($setting_data["show_property_2_in_pre_factor"])
            <td>{{$item["item"]->product->property2_caption=="0"?"":$item["item"]->product->property2_caption}}</td>
        @endif

        @if($setting_data["show_property_3_in_pre_factor"])
            <td>{{$item["item"]->product->property3_caption=="0"?"":$item["item"]->product->property3_caption}}</td>
        @endif


        <td>{{to_persian($item["item"]->degree->caption)}}</td>
        <td>{{to_persian($item["amount"])}}</td>
        <td>{{to_persian($item["item"]->product->unit->bach_caption)}}</td>
        <td>{{to_money($item["fea"],0)}}</td>
        <td>{{to_money($item["price"],0)}}</td>
        <td>{{to_money($item["total_off_price"],0)}}</td>
        <td>{{to_money($item["total_price"],0)}}</td>
        <td>{{to_money($item["tax_price"],0)}}</td>
        <td>{{to_money($item["total_price_with_tax"],0)}}</td>
    </tr>
@endforeach
<tr style="font-size: 16px; font-weight: bold;background: #e2d7d7">
    <td colspan="{{$col_span_count+3}}"> جمع کل ({{$order->customer->tariff->currency->caption??""}})</td>
    <td>{{to_persian($sum_amount)}}</td>
    <td></td>
    <td></td>
    <td>{{to_money($sum_price)}}</td>
    <td>{{to_money($sum_off_price)}}</td>
    <td>{{to_money($sum_total_price)}}</td>
    <td>{{to_money($sum_tax_price)}}</td>
    <td>{{to_money($sum_total_price_with_tax)}}</td>
</tr>