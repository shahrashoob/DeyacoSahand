<div class="{{isset($class_col)?$class_col:"col-md-6 offset-md-6"}}">
    <div class="form-group">
        <label>{{$lable??$label??""}}</label>
        @include('component.input._attention')
        <input name="{{$id}}" id="{{$id}}" value="{{isset($value)?$value:""}}" type="password" class="form-control" {{isset($readonly)?"readonly":""}}>
    </div>
</div>
