<table style="text-align: center">
    <tr>
        <td colspan="3">
            {{$software_name}}
        </td>
    </tr>

    <tr>
        <td colspan="3">
            @if(isset($caption))
                {{$caption}}:
            @endif
            {{$machine->caption}}
        </td>
    </tr>

    <tr>
        <td colspan="3">
            سریال کارت تولید:
            {{$machine_allocation->production->serial()}}
            @if($machine_allocation->other_allocation_count)
                @for($k=1; $k<= $machine_allocation->other_allocation_count; $k++)
                    @php $key="other_allocation_".$k; $other_machine_allocation=$machine_allocation->$key; @endphp
                    <br/>
                    {{$other_machine_allocation->production->serial()}}
                @endfor
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="3" style="font-size: 14px">

            @switch($machine_allocation->production->product->goods_kind_id)
                @case(4)
                    {{--کد طرح (کالیته--}}
                    {!!$machine_allocation->product->getPropertyValue(220219,"caption_value_normal")!!}

                    {{--کد طرح (کالیته--}}

                    {!!$machine_allocation->product->getPropertyValue(220380,"caption_value_normal")!!}
                    @break
                @default

                    {!!$machine_allocation->product->getPropertyValue($machine_allocation->production->product->goods_kind->property1_id,"caption_value_normal")!!}



                    {!!$machine_allocation->product->getPropertyValue($machine_allocation->production->product->goods_kind->property2_id,"caption_value_normal")!!}
                    @break
            @endswitch

        </td>
    </tr>

    <tr>
        <td colspan="3" style="font-size: 14px">


            {!!$machine_allocation->product->caption!!}
            @if($machine_allocation->version_code)
                <br/>
                (V{{$machine_allocation->version_code}})
            @endif
            @if($machine_allocation->other_allocation_count)
                @for($k=1; $k<= $machine_allocation->other_allocation_count; $k++)
                    @php $key="other_allocation_".$k; $other_machine_allocation=$machine_allocation->$key; @endphp
                    <br/>
                    {!!$other_machine_allocation->product->caption!!}
                    @if($other_machine_allocation->version_code)
                        <br/>
                        (V{{$other_machine_allocation->version_code}})
                    @endif
                @endfor
            @endif


        </td>
    </tr>


    @foreach($current_input_list[$machine_allocation->allocation_id] as $item)
        <tr>
            <td style="width: 30px">
                {{$item->input_line_code}}
            </td>
            <td colspan="2">
                {{$item->material->caption??"***"}}
            </td>


        </tr>
    @endforeach

    <tr>
        <td>
            {{$machine_allocation->product->unit->measurement}}
            (ها)
        </td>


        @if(count($machine_allocation->allocation->allocation_doffs)>0)
            <td colspan="2" style="padding: 0px">
            {{--                @foreach($machine_allocation->allocation->allocation_doffs as $allocation_doff)--}}

            {{--                    <input type="checkbox" {{$allocation_doff->number_of_doffs_done?"checked":"disabled"}}--}}
            {{--                           style="font-size: 14px">--}}

            {{--                    @include("line_product_station.product.unit_of_measure_type._lot_number",["product"=>$machine_allocation->product,"amount"=>$allocation_doff->amount_of_each_doffs,"number_of_brand_done"=>$allocation_doff->number_of_brand_done])--}}
            {{--                    <br/>--}}

            {{--                @endforeach--}}

            @include("goods_kind_process.general.machine.allocation_card._allocation_doff_brand",["product"=>$machine_allocation->product,"machine_allocation"=>$machine_allocation,"type"=>"card_info"])


        @else
            <td colspan="2">
                @for($k=0; $k<$machine_allocation->max_number_of_doffs;$k++)

                    @if($machine_allocation->number_of_doffs_done >$k)

                        <input type="checkbox" checked
                               style="font-size: 14px">

                        @include("line_product_station.product.unit_of_measure_type._lot_number",["product"=>$machine_allocation->product,"amount"=>$machine_allocation->amount_of_each_doffs])
                    @else
                        <input type="checkbox" disabled
                               style="font-size: 14px">

                        @include("line_product_station.product.unit_of_measure_type._lot_number",["product"=>$machine_allocation->product,"amount"=>$machine_allocation->amount_of_each_doffs])

                    @endif

                @endfor
                @endif
            </td>
    </tr>

    @if(isset($product_fault_list[$machine_allocation->id]) && count($product_fault_list[$machine_allocation->id]) > 0)
        <tr>
            <td colspan="3">
                نقص های غیر مجاز:


                @foreach($product_fault_list[$machine_allocation->id] as $product_fault_item)

                    {{$product_fault_item->product_fault->caption}},

                @endforeach
            </td>
        </tr>
    @endif

    <tr>
        <td colspan="3">
            @if(isset($caption))
                <a href="{{asset("upload/product/".($machine_allocation->product->image->filename??''))}}"
                   class="view-image" style="float: right; padding-right: 5px"
                   data-title="{{isset($value)?$value:""}}">
                    <span><i class="fa  fa-image"></i> تصویر کالا</span>
                </a>
            @endif
            سازمان دیجیتال دیاکو

            @if(isset($caption))
                <a href="{{route("fabric_raw.jacquard.machine.allocation_card.index",[$machine,$machine_allocation->allocation])}}"
                   class="print" style="float: left; padding-left: 5px"><i class="fa fa-print"></i> چاپ </a>
            @endif

        </td>
    </tr>
</table>
