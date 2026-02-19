@php
    $unit_of_measure_types=str_split((string) $product->unit_of_measure_type_id_in_production);
    $ut=0;
    @endphp

<b>
(
    @foreach($unit_of_measure_types as $unit_of_measure_type)




        @switch($unit_of_measure_type)
            @case(1)
                @php $ut++ @endphp
                @if($ut>1) - @endif

                {{round($amount,6)}} {{$product->unit->caption??""}}
                @break
            @case(2)

                @break
            @case(3)
                @if($product->sub_unit2_id ==1400 && $product->frame_ratio_unit2!=0)
                    @php $ut++ @endphp
                    @if($ut>1) - @endif
                    {{floor(round($amount/$product->frame_ratio_unit2,6))}} {{$product->sub_unit2->caption??""}}
                @endif
                @break
        @endswitch


    @endforeach

    @if(isset($number_of_brand_done) && $number_of_brand_done)
       - {{$number_of_brand_done }} برند
    @endif

    @if(isset($normal_amount) && $product->sub_unit2_id ==1400 && $product->frame_ratio_unit2!=0)
        -
        {{ceil(round($amount/$normal_amount,6))}} برند
    @endif


)
</b>