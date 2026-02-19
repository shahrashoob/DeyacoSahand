<div class="{{isset($class_col)?$class_col:"col-md-6 offset-md-6"}}">
    <div class="form-group">
        @if(isset($lable) || isset($label))
            <label class="form-label">{!!  $lable??$label??"" !!}</label>
            @include('component.input._attention')
        @endif
            @if(isset($url) && $url!="")
                <b><a href="{{$url}}" target="{{$target??"_blank"}}">{!!isset($url_text)?$url_text:""!!}</a> </b>
            @endif

        @include("component.input._aotocomplet",["id"=>$id,"option"=>$option,"label"=>null])

    </div>
</div>
