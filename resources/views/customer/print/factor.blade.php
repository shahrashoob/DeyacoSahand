<html>
<head>
    @include("pdf._head",["font_size"=>14])
</head>
<body>

<div class="content">

    @include("customer.print._order_info",["caption"=>"پیشنهاد بهای فروش کالا و خدمات"])

    <table>
        @include("customer.print._seller")

        @include("customer.print._buyer")

        @include("customer.print._col_text")



        @php               $row=1;
                           $sum_price=0;
                           $sum_amount=0;
                           $sum_total_price=0;
                           $sum_off_price=0;
                           $sum_tax_price=0;
                           $sum_total_price_with_tax=0;

        @endphp
        @foreach($order->orderFactor as $item)
            @php
                $sum_price+=$item->price;
                $sum_amount+=$item->carton;
                $sum_total_price+=$item->total_price;
                $sum_off_price+=$item->total_off_price;
                $sum_tax_price+=$item->tax_price;
                $sum_total_price_with_tax+=$item->total_price_with_tax;
            @endphp
            <tr>
                <td>{{to_persian($row++)}}</td>
                <td>{{to_persian($item->service_id?$item->service->code:$item->product->code)}}</td>

                @if($setting_data["show_product_caption_in_pre_factor"])
                    <td>{{to_persian($item->getCaption())}}</td>

                @endif

                @if($setting_data["show_packing_type_caption_in_pre_factor"] || $setting_data["show_packing_type_code_in_pre_factor"])
                    @php $k=0; $packing_types=$item->getPackingType("packing_types"); @endphp
                    <td>
                        @if(is_array($packing_types))
                        @foreach($packing_types as $packing_type)
                            @if($setting_data["show_packing_type_caption_in_pre_factor"] && $setting_data["show_packing_type_code_in_pre_factor"])
                                {{to_persian($packing_type->caption)}}  -    {{to_persian($packing_type->code)}}
                            @elseif($setting_data["show_packing_type_caption_in_pre_factor"])
                                {{to_persian($packing_type->caption)}}
                            @elseif($setting_data["show_packing_type_code_in_pre_factor"])
                                {{to_persian($packing_type->code)}}
                            @endif

                            @if(++$k >1)
                                <br/>
                                @endif
                        @endforeach
                        @else
                            {{$packing_types}} -----
                        @endif

                    </td>
                @endif


                @if($setting_data["show_property_1_in_pre_factor"])
                    <td>{{$item->product->property1_caption=="0"?"":$item->product->property1_caption}}</td>
                @endif

                @if($setting_data["show_property_2_in_pre_factor"])
                    <td>{{$item->product->property2_caption=="0"?"":$item->product->property2_caption}}</td>
                @endif

                @if($setting_data["show_property_3_in_pre_factor"])
                    <td>{{$item->product->property3_caption=="0"?"":$item->product->property3_caption}}</td>
                @endif


                <td>{{to_persian($item->degree->caption)}}</td>
                <td>{{to_persian($item->carton)}}</td>
                <td>{{to_persian($item->product->unit->bach_caption)}}</td>
                <td>{{to_money($item->fea)}}</td>
                <td>{{to_money($item->price)}}</td>
                <td>{{to_money($item->total_off_price)}}</td>
                <td>{{to_money($item->total_price)}}</td>
                <td>{{to_money($item->tax_price)}}</td>
                <td>{{to_money($item->total_price_with_tax)}}</td>
            </tr>
        @endforeach
        <tr style="font-size: 16px; font-weight: bold;background: #e2d7d7">
            <td colspan="{{$col_span_count+3}}"> جمع کل ({{$order->customer->tariff->currency->caption??""}})</td>
            <td>{{to_money(round($sum_amount))}}</td>
            <td></td>
            <td></td>
            <td>{{to_money(round($sum_price))}}</td>
            <td>{{to_money(round($sum_off_price))}}</td>
            <td>{{to_money(round($sum_total_price))}}</td>
            <td>{{to_money(round($sum_tax_price))}}</td>
            <td>{{to_money(round($sum_total_price_with_tax))}}</td>
        </tr>


    </table>


    @include("customer.print._footer_of_factor")
</div>

</body>
</html>
