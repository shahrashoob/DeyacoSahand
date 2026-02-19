@php
    $unit_of_measure_types=str_split((string) $product->unit_of_measure_type_id_in_production);
    $ut=0;
@endphp


@foreach($unit_of_measure_types as $unit_of_measure_type)




    @switch($unit_of_measure_type)
        @case(1)
            @php $ut++ @endphp
            @if($ut>1)
                -
            @endif
            {{round($amount,2)}} {{$product->unit->caption??""}}
            @break
        @case(2)

            @break
        @case(3)

            @if($product->sub_unit2_id ==1400 && $product->frame_ratio_unit2!=0)
                @php $ut++ @endphp
                @if($ut>1)
                    -
                @endif
                @if(isset($sub_amount2))
                    @php $x=explode('.', (string)$sub_amount2)[1] ?? '';  @endphp
                    {{$x!=''? "".$x.". + ":""}}

                    {{floor($sub_amount2)}}
                    قاب

                @else
                    {{floor(round($amount/$product->frame_ratio_unit2,6))}}
{{--                    {{$product->sub_unit2->caption??""}}--}}
                    قاب
                @endif
            @endif
            @break
    @endswitch

@endforeach
