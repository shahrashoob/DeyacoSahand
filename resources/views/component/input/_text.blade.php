<div class="{{isset($class_col)?$class_col:"col-md-6 offset-md-6"}}">
    <div class="form-group">
        @if(isset($lable) || isset($label))
            <label class="">{!! $lable??$label??"" !!}</label>
            @include('component.input._attention')

            {{--            form-label--}}
        @endif

        <input name="{{$id}}" id="{{$id}}" value="{{isset($value)?$value:""}}" type="text"
               class="{{isset($seperated_number) && $seperated_number?"numeric":""}} form-control"
                {{isset($readonly) && $readonly?"readonly":""}} {{isset($autofocus)?"autofocus":""}}>
    </div>
</div>
