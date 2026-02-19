
    @include("component.input._lable", [ 'label'=>"ملیت", "value"=>$employment->nationality->caption, "class_col"=>"col-md-6"])
    @include("component.input._lable", [ 'label'=>"کشور", "value"=>$employment->country->caption, "class_col"=>"col-md-6"])
    @include("component.input._lable", [ 'label'=>"نوع همکاری", "value"=>$employment->cooperation_type->caption, "class_col"=>"col-md-6"])
    @include("component.input._lable", [ 'label'=>"نوع شخصیت", "value"=>$employment->personal_type->caption, "class_col"=>"col-md-6"])




    @include("component.input._lable", ["id"=>"caption", 'label'=>"نام شرکت", "value"=>$employment->company->caption??"", "class_col"=>"col-md-6"])
    @include("hr.employment.register.personal_type._preview_basic_personal_info")



