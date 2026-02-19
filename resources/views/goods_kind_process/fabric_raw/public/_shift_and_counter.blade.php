<div class="w-100"></div>
{{--@if(!isset($not_shift) && isset($shift_work_option) )--}}
{{--    <div class="{{isset($class_col)?$class_col:"col-md-6"}}">--}}
{{--        @include("component.input._aotocomplet2",[--}}
{{--            "id"=>"shift_work_id".(isset($id)?$id:""),--}}
{{--            "label"=>" شیفت کاری   ",--}}
{{--            "option"=>$shift_work_option["items"],--}}
{{--            "val"=>$shift_work_option["value"],--}}
{{--            "text"=>$shift_work_option["text"],--}}
{{--            "class_col"=>""--}}
{{--            ])--}}
{{--    </div>--}}
{{--@endif--}}

@if(isset($machine))
    @for($k=1;$k<=$machine->machine_type->get_property_value(6);$k++)
        @php $value="contour_".$k."_value";@endphp
        @include("component.input._number",["id"=>"contour_".$k."_value".(isset($id)?$id:""),"label"=>"مقدار ".$machine->machine_type->get_property_value(8)." ".$k,"value"=>$$value??""])
    @endfor
@else

    @for($k=1;$k<=$machine_type->get_property_value(6);$k++)
        @php $value="contour_".$k."_value";@endphp
        @include("component.input._number",["id"=>"contour_".$k."_value".(isset($id)?$id:""),"label"=>"مقدار ".$machine_type->get_property_value(8)." ".$k,"value"=>$$value??""])
    @endfor
@endif


