@include("component.input._lable",["id"=>"user_id","label"=>"نام شرکت ","value"=>$employment->company->caption??""])
@include("component.input._lable",["id"=>"status_id","label"=>"شماره ثبت ","value"=>$employment->company->register_code])
@include("component.input._lable",["id"=>"","label"=>"شناسه ملی شرکت","value"=>$employment->national_code??""])
