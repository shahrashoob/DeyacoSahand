@include("component.input._lable", ["id"=>"email", 'label'=>"نام کاربری", "value"=>$employment->worker->email??"", "class_col"=>"col-md-6"])

@include("component.input._lable", ["id"=>"firstname", 'label'=>"نام ", "value"=>$employment->worker->firstname??"", "class_col"=>"col-md-6"])
@include("component.input._lable", ["id"=>"lastname", 'label'=>"نام خانوادگی", "value"=>$employment->worker->lastname??"", "class_col"=>"col-md-6"])
@if(in_array($employment->cooperation_type_id,[1,11]))
@include("component.input._lable", ["id"=>"father_name", 'label'=>"نام پدر", "value"=>$employment->worker->father_name??"", "class_col"=>"col-md-6"])

@include("component.input._lable", ["id"=>"birth_certificate_number", 'label'=> $employment->get_caption_of_birth_certificate_number($employment->nationality_id), "value"=>$employment->worker->birth_certificate_number??"", "class_col"=>"col-md-6"])
@endif
@if($employment->personal_type_id==2)
@include("component.input._lable", ["id"=>"national_code", 'label'=>"کد ملی مدیر عامل", "value"=>$employment->worker->national_code??"", "class_col"=>"col-md-6"])
@endif
@include("component.input._lable", [ 'label'=>"جنسیت", "value"=>$employment->worker->gender->caption??"", "class_col"=>"col-md-6"])
@if(in_array($employment->cooperation_type_id,[1,11]))
@include("component.input._lable", [ 'label'=>"وضعیت تاهل", "value"=>$employment->worker->marital_status->caption??"", "class_col"=>"col-md-6"])

@include("component.input._lable", ["id"=>"place_of_birth", 'label'=>"محل تولد ", "value"=>$employment->worker->place_of_birth??"", "class_col"=>"col-md-6"])
@endif
@include("component.input._lable", ["id"=>"date_of_birth", 'label'=>"تاریخ تولد", "value"=>$employment->worker->get_date_of_birth()??"",  "class_col"=>"col-md-6",])
