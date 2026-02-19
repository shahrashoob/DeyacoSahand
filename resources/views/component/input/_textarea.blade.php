
<div class="{{isset($class_col)?$class_col:"col-md-12"}}">
    <div class="form-group">
        @if(isset($lable) || isset($label))
            <label class="form-label">{!! $lable??$label??"" !!}</label>
            @include('component.input._attention')
        @endif
            <textarea id="{{$id}}" name="{{$id}}" class="form-control"
                      name="validation-text"
                      {{isset($readonly) && $readonly?"readonly":""}}
                      style=" {{isset($width)?"width:$width".";":""}} {{isset($height)?"height:$height".";":""}}">{{$value??""}}</textarea>
    </div>
</div>
