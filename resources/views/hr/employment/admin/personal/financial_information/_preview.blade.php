@include("component.input._lable", [ 'label'=>"کد مرکز هزینه", "value"=>$employment->worker->cost_center->code??"", "class_col"=>"col-md-6"])
@include("component.input._lable", [ 'label'=>"نام مرکز هزینه", "value"=>$employment->worker->cost_center->caption??"", "class_col"=>"col-md-6"])
@include("component.input._lable", [ 'label'=>"کد تفضیلی", "value"=>$employment->worker->detailed_code??"", "class_col"=>"col-md-6"])


