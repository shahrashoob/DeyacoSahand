@php
    $product=$production->product??$product;
    $unit_of_measure_types=str_split((string) $product->unit_of_measure_type_id_in_production);
    $ut=0;

@endphp


@if(isset($label))
    <div class="{{(isset($class_col)?$class_col:"col-md-6 offset-md-6")}}">
        <div class="form-group">
            <label>{{$lable??$label??""}}:</label>
            <b>
                @endif


                @foreach($unit_of_measure_types as $unit_of_measure_type)




                    @switch($unit_of_measure_type)
                        @case(1)
                            @php $ut++ @endphp
                            @if($ut>1)
                                -
                            @endif
                            {{$units["amount"]}} {{$product->unit->caption??""}}
                            @break
                        @case(2)
                            @if(isset($units["sub_amount"]) && $units["sub_amount"]!="")
                                @php $ut++ @endphp
                                @if($ut>1)
                                    -
                                @endif
                                {{$units["sub_amount"]}} {{$product->sub_unit->caption??""}}
                            @endif
                                @break
                        @case(3)
                            @if($product->sub_unit2_id ==1400 && $product->frame_ratio_unit2!=0)
                                @php $ut++ @endphp
                                @if($ut>1)
                                    -
                                @endif
                                {{floor(round((float)$units["amount"]/$product->frame_ratio_unit2,6))}} {{$product->sub_unit2->caption??""}}
                            @endif
                            @break
                    @endswitch

                @endforeach






 @if(isset($label))
            </b>
        </div>
    </div>
@endif




