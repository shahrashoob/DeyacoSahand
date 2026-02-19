<table style="">
    <tr>
        <td colspan="3">
            {{$software_name}}
        </td>
    </tr>
    <tr>
        <td colspan="3">
            {{$caption}}: {{$machine->caption}}
        </td>
    </tr>
    <tr>
        <td colspan="3">
            سریال کارت تولید: {{$machine_allocation->production->serial()}}
        </td>
    </tr>
    <tr>
        <td colspan="3" style="font-size: 14px">

            {{--کد طرح (کالیته--}}
            {!!$machine_allocation->product->getPropertyValue(220219,"caption_value_normal")!!}

            {{--کد طرح (کالیته--}}

            {!!$machine_allocation->product->getPropertyValue(220380,"caption_value_normal")!!}

        </td>
    </tr>


    @foreach($current_input_list[$machine_allocation->production_id] as $item)
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
            متراژ(ها)
        </td>
        <td colspan="2">

            @for($k=0; $k<$machine_allocation->max_number_of_doffs;$k++)

                @if($machine_allocation->number_of_doffs_done >$k)

                    <input type="checkbox" checked
                           style="font-size: 14px"> {{$machine_allocation->amount_of_each_doffs}} متر
                @else
                    <input type="checkbox" disabled
                           style="font-size: 14px"> {{$machine_allocation->amount_of_each_doffs}} متر
                @endif

            @endfor
        </td>
    </tr>


    <tr>
        <td colspan="3">
            سازمان دیجیتال دیاکو

            <a href="{{route("fabric_raw.warps.machine.allocation_card.index",[$machine,$machine_allocation->allocation])}}"  class="print" style="float: left; padding-left: 5px"><i class="fa fa-print"></i> چاپ </a>

        </td>
    </tr>
</table>
