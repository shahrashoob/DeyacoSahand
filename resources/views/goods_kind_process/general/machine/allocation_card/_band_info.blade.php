{{--<div class="content">--}}
{{--    <div style="text-align: center"></div>--}}

{{--    <div style="text-align: center">--}}

{{--    </div>--}}
{{--    <table style="">--}}
{{--        <tr>--}}
{{--            <td colspan="3">--}}
{{--                {{$software_name}}--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            <td colspan="3">--}}
{{--                ماشین: {{$machine->caption}}--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            <td colspan="3">--}}
{{--                سریال کارت تولید: {{$production->serial()}}--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            <td colspan="3" style="text-align: center">--}}
{{--                کالا: {{$production->product->fullCaption()}}--}}
{{--                @if($machine_allocation->version_code)--}}

{{--                    (V{{$machine_allocation->version_code}})--}}
{{--                @endif--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            <td colspan="3" style="font-size: 14px">--}}

{{--                --}}{{--کد طرح (کالیته--}}
{{--                {!!$product->getPropertyValue(220219,"caption_value_normal")!!}--}}

{{--                --}}{{--کد طرح (کالیته--}}

{{--                {!!$product->getPropertyValue(220380,"caption_value_normal")!!}--}}

{{--            </td>--}}
{{--        </tr>--}}


{{--        @foreach($current_input_list as $item)--}}
{{--            <tr>--}}
{{--                <td style="width: 30px">--}}
{{--                    {{$item->input_line_code}}--}}
{{--                </td>--}}
{{--                <td colspan="2">--}}
{{--                    {{$item->material->caption??"***"}}--}}
{{--                </td>--}}


{{--            </tr>--}}
{{--        @endforeach--}}

{{--        <tr>--}}
{{--            <td>--}}
{{--                متراژ(ها)--}}
{{--            </td>--}}
{{--            <td colspan="2">--}}

{{--                @for($k=0; $k<$machine_allocation->max_number_of_doffs;$k++)--}}

{{--                    <input type="checkbox" style="font-size: 14px"> {{$machine_allocation->amount_of_each_doffs}} متر--}}

{{--                @endfor--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--        @if(count($product_fault_list) > 0)--}}
{{--            <tr>--}}
{{--                <td colspan="3">--}}
{{--                    نقص های غیر مجاز:--}}


{{--                    @foreach($product_fault_list as $product_fault_item)--}}

{{--                        {{$product_fault_item->product_fault->caption}},--}}

{{--                    @endforeach--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--        @endif--}}
{{--        <tr>--}}
{{--            <td colspan="3">--}}
{{--                سازمان دیجیتال دیاکو--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--    </table>--}}


{{--</div>--}}
@include("production.machine.short_link._card_info")