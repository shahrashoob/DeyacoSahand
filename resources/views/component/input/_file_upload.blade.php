
<div class="{{isset($class_col)?$class_col:"col-md-6 offset-md-6"}} form-group ">

    <label for="image_label">{{$label??"تصویر"}}</label>
    @include('component.input._attention')
    <div class="input-group">
            <input type="file" id="{{$id}}" name="{{$id}}{{isset($multiple)?"[]":""}}" class="form-control" {{isset($multiple)?"multiple='multiple'":""}} maxlength="100"/>
    </div>
</div>
