@if($machine_allocation->allocation->allocation_doffs()->count()>0)
@php $is_current_allocation=$machine_allocation->status_id ==5310010?1:0;@endphp
    @if(isset($type) && $type == "card_info")
        <table style="text-align:center;width: 100%; margin:0px; border:none;font-weight: bold">
            @else
                <table style="text-align:center;all: unset;font-weight: bold">
                    @endif
                    <tr>
                        <td colspan="2">داف</td>
                        @if($machine_allocation->production->normal_amount)
                            <td colspan="2">لوگو</td>
                        @endif


                        @if($product->sub_unit2_id ==1400 && $product->frame_ratio_unit2)
                            <td>قاب</td>
                        @endif
                        <td>مقدار</td>
                    </tr>
                    @php $number_doff=1; @endphp
                    @foreach($item->allocation->allocation_doffs as $allocation_doff)

                        @php $brand_number=0;

                        @endphp

                        @if( $allocation_doff->number_of_brand && $allocation_doff->number_of_brand > 0)
                            @foreach($allocation_doff->allocation_brands as $allocation_brand)
                                <tr class="{{$machine_allocation->number_of_doffs_done >= $number_doff?"alert-primary":($machine_allocation->number_of_doffs_done+$is_current_allocation == $number_doff?"alert-warning":"")}}">
                                    @if($brand_number==0)

                                        <td style="vertical-align: middle"
                                            rowspan="{{max($allocation_doff->allocation_brands()->count(),1)}}"
                                            >
{{--                                            @if(isset($type) && $type == "card_info")--}}
{{--                                                <input type="checkbox"--}}
{{--                                                       disabled {{$machine_allocation->number_of_doffs_done >= $number_doff?"checked='checked'":""}} />--}}
{{--                                            @endif--}}
                                        </td>
                                        <td style="vertical-align: middle"
                                            rowspan="{{max($allocation_doff->allocation_brands()->count(),1)}}"
                                            >

                                            {{$number_doff}}

                                        </td>
                                    @endif
                                    <td>
                                        @if(isset($type) && $type == "card_info")
                                            <input type="checkbox"
                                                   disabled {{($allocation_doff->number_of_brand_done >= $brand_number+1 || ($machine_allocation->number_of_doffs_done >= $number_doff))?"checked='checked'":""}} />
                                        @endif
                                    </td>
                                    <td>{{++$brand_number}}</td>


                                    @if($product->sub_unit2_id ==1400 && $product->frame_ratio_unit2)
                                        <td>{{round($allocation_brand->amount_of_brand/$product->frame_ratio_unit2,8)}}</td>
                                    @endif
                                        <td>{{$allocation_brand->amount_of_brand}}</td>
                                </tr>
                            @endforeach

                        @else
                            <tr class="{{$machine_allocation->number_of_doffs_done >= $number_doff?"alert-primary":($machine_allocation->number_of_doffs_done+$is_current_allocation == $number_doff?"alert-warning":"")}}">
                                <td style="vertical-align: middle"
                                    >
                                    @if(isset($type) && $type == "card_info")
                                        <input type="checkbox"
                                               disabled {{$machine_allocation->number_of_doffs_done >= $number_doff?"checked='checked'":""}} />
                                    @endif
                                </td>
                                <td style="vertical-align: middle"

                                    class="">

                                    {{$number_doff}}

                                </td>
                                @if($machine_allocation->production->normal_amount)
                                    <td colspan="2">---</td>
                                @endif

                                @if($product->sub_unit2_id ==1400 && $product->frame_ratio_unit2)
                                    <td>{{round($allocation_doff->amount_of_each_doffs/$product->frame_ratio_unit2,8)}}</td>
                                @endif
                                <td>{{$allocation_doff->amount_of_each_doffs}}</td>
                            </tr>
                        @endif

                        @php $number_doff++ @endphp
                    @endforeach
                </table>

    @else
        {{$machine_allocation->amount_of_each_doffs}}
    @endif


