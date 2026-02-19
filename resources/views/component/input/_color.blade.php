<div class="{{isset($class_col)?$class_col:"col-md-6 offset-md-6"}}">
    <div class="form-group">

        @if(isset($lable) || isset($label))
            <label class="">{!! $lable??$label??"" !!}</label>

        @endif
        <input name="{{$id}}" id="{{$id}}" value="{{isset($value)?$value:""}}" type="color"

               >
    </div>
</div>
