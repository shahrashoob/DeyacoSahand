<div class="{{isset($class_col)?$class_col:"col-md-6 offset-md-6"}}">
<div class="{{isset($class)?$class:""}}">
        <label class="form-label">{{$lable??$label??""}}</label>
    @include('component.input._attention')
           @include("component.input._select_simple")

    </div>
</div>
