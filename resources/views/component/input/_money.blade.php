<div class="{{(isset($class_col)?$class_col:"col-md-6 offset-md-6")}}">
    <div class="form-group">
        <label>{{$lable??$label??""}}:</label>
        @if(isset($value))
            <b>@to_money($value??0)</b>
        @endif
        @if(isset($message))
            {!! $message !!}
        @endif

    </div>
</div>
