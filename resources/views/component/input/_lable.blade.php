<div class="{{(isset($class_col)?$class_col:"col-md-6 offset-md-6")}}">
    <div class="form-group">
        <label>{{$lable??$label??""}}:</label>
        @if(!isset($url) || $url=="")
            <b>{{isset($value)?$value:""}}</b>
        @else
            <b><a href="{{$url}}" target="{{$target??"_blank"}}">{{isset($value)?$value:""}}</a> </b>
        @endif
        @if(isset($message))
            {!! $message !!}
        @endif

    </div>
</div>
