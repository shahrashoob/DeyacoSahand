{{--@include("component.input._lable", [ 'label'=>"ملیت", "value"=>$employment->nationality->caption, "class_col"=>""])--}}
@include("component.input._lable", [ 'label'=>"کشور", "value"=>$employment->country->caption, "class_col"=>""])
@include("component.input._lable", [ 'label'=>"نوع همکاری", "value"=>$employment->cooperation_type->caption, "class_col"=>""])
@include("component.input._lable", [ 'label'=>"نوع شخصیت", "value"=>$employment->personal_type->caption, "class_col"=>""])

@include("component.input._lable", [ 'label'=>$employment->getCaptionNationalCode(), "value"=>$employment->national_code, "class_col"=>""])
