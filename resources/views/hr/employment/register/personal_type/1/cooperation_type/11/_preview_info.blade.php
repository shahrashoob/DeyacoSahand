
    @include("component.input._lable", [ 'label'=>"ملیت", "value"=>$employment->nationality->caption, "class_col"=>"col-md-6"])
    @include("component.input._lable", [ 'label'=>"کشور", "value"=>$employment->country->caption, "class_col"=>"col-md-6"])
    @include("component.input._lable", [ 'label'=>"نوع همکاری", "value"=>$employment->cooperation_type->caption, "class_col"=>"col-md-6"])
    @include("component.input._lable", [ 'label'=>"نوع شخصیت", "value"=>$employment->personal_type->caption, "class_col"=>"col-md-6"])

    @include("component.input._lable", [ 'label'=>$employment->getCaptionNationalCode(), "value"=>$employment->national_code, "class_col"=>"col-md-6"])


    @include("component.input._lable", [ 'label'=>"پست سازمانی", "value"=>$employment->post->caption ?? "", "class_col"=>"col-md-6"])






    @include("hr.employment.register.personal_type._preview_basic_personal_info")

    @include("component.input._lable", ["id"=>"date_of_readiness_to_start_work", 'label'=>"تاریخ آمادگی جهت شروع به کار", "value"=>$employment->get_date_of_readiness_to_start_work(),  "class_col"=>"col-md-6",])



