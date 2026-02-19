@php
    $product=$production->product??$product;
    $unit_of_measure_types=str_split((string) $product->unit_of_measure_type_id_in_production);


@endphp


@if(isset($label))
    <div class="{{(isset($class_col)?$class_col:"col-md-6 offset-md-6")}}">
        <div class="form-group">
            <label>{{$lable??$label??""}}:</label>
            <b>
                @endif



                @foreach($allocation_doffs as $doff)
                    @php $ut=0 @endphp
                    @foreach($unit_of_measure_types as $unit_of_measure_type)



                        @switch($unit_of_measure_type)
                            @case(1)
                                @php $ut++ @endphp
                                @if($ut>1) - @endif
                                {{$doff->amount_of_each_doffs}} {{$product->unit->caption??""}}
                                @break
                            @case(2)

                                @break
                            @case(3)
                                @if($product->sub_unit2_id ==1400 && $product->frame_ratio_unit2!=0)
                                    @php $ut++ @endphp
                                    @if($ut>1) - @endif
                                    {{floor(round((float)$doff->amount_of_each_doffs/$product->frame_ratio_unit2,6))}} {{$product->sub_unit2->caption??""}}
                                @endif
                                @break
                        @endswitch

                    @endforeach

                    |
                @endforeach



                @if(isset($label))
            </b>
        </div>
    </div>
@endif




