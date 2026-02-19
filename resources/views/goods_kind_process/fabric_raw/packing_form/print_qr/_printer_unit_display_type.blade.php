@if($packing_form->packing_type->printer_unit_display_type_id ==1)
    @php $display_amount=$packing_form->getFinalAmount();@endphp
    {{$packing_form->getUnitCaption("unit","measurement",":")}}
    @if($display_amount!=0)
        <span style="font-size: 16px; display: inline">
                                            {{$packing_form->getFinalAmount()}}
                                        </span>
        {{$packing_form->getUnitCaption("unit","caption")}}
    @endif
@elseif($packing_form->packing_type->printer_unit_display_type_id ==2 )
    @php $display_amount=$packing_form->getSubAmount();@endphp

    {{$packing_form->getUnitCaption("sub_unit","measurement",":")}}

    @if($display_amount!=0)
        <span style="font-size: 16px; display: inline">
                                            {{$packing_form->getSubAmount()}}
                                        </span>
        {{$packing_form->getUnitCaption("sub_unit","caption")}}
    @endif
@endif
