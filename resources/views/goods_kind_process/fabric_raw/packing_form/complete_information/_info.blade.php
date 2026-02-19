
@if(!isset($smart_object_value))
@include("component.input._number",["id"=>"gross_weight","lable"=>"وزن ناخالص","value"=>"","class_col"=>"col-md-4"])

@else
    @include("component.input._lable",["lable"=>"باسکول ","value"=>$smart_object->caption??"","url"=>$url_scale??"","class_col"=>"col-md-4"])
    @include("component.input._lable",["lable"=>"وزن ناخالص","value"=>$smart_object_value. " کیلوگرم","class_col"=>"col-md-4"])
    @include("component.input._hidden",["id"=>"gross_weight","value"=>$smart_object_value,"class_col"=>"col-md-4"])

@endif
