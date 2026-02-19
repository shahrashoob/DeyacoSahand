<div class="{{isset($class_col)?$class_col:"col-md-6 offset-md-6"}}">
    <div class="form-group">
        @if(isset($lable) || isset($label))
            <label id="{{$id}}_label">{!! $lable??$label??"" !!}</label>
            @include('component.input._attention')
            @if(isset($is_smart_object) && $is_smart_object)
                {{--                آیا این فیلد وزن ناخالص است که باید از ترازو یا اشیاء خوانده شود.--}}
                <a href="#sdf" id="{{$id}}_smart_object" class="smart_object_icon " onclick="set_id_for_smart_object('{{$id}}');">
                    <i class="fas  fa-weight"></i>
                </a>
            @endif
        @endif
        <input name="{{$name??$id}}" id="{{$id}}" value="{{isset($value)?$value:""}}" type="number" min={{isset($min)?$min:"0"}} {{isset($autofocus)?"autofocus":""}}
               class="form-control" {{isset($readonly) && $readonly?"readonly":""}}  {{isset($required)?"required":""}}>
        {!! $other_content??"" !!}

    </div>



</div>
