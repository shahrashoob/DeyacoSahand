@include("component.input._text", ["id"=>"email", 'label'=>"نام کاربری", "value"=>$email??"", "class_col"=>"","mark"=>"(باید شامل حروف انگلیسی باشد) *"])

@include("component.input._text", ["id"=>"firstname", 'label'=>"نام ", "value"=>$employment->worker->firstname??"", "class_col"=>"","mark"=>"*"])
@include("component.input._text", ["id"=>"lastname", 'label'=>"نام خانوادگی", "value"=>$employment->worker->lastname??"", "class_col"=>"","mark"=>"*"])
@include("component.input._text", ["id"=>"father_name", 'label'=>"نام پدر", "value"=>$employment->worker->father_name??"", "class_col"=>"","mark"=>"*"])
@include("component.input._number", ["id"=>"birth_certificate_number", 'label'=> $employment->get_caption_of_birth_certificate_number($employment->nationality_id), "value"=>$employment->worker->birth_certificate_number??"","mark"=>"*", "class_col"=>""])
@include("component.input._select", [
                  "id"=>"gender_id",
                  "label"=>"جنسیت",
                  "option"=>$gender_option["items"],
                  "val"=>$gender_option["value"],
                  "text"=>$gender_option["text"],
                  "class_col"=>"",
                  "mark"=>"*"
                  ])

<div class="w-100"><br/></div>
@include("component.input._select", [
   "id"=>"marital_status_id",
   "label"=>"وضعیت تاهل",
   "option"=>$marital_status_option["items"],
   "val"=>$marital_status_option["value"],
   "text"=>$marital_status_option["text"],
   "class_col"=>"",
   "mark"=>"*"
   ])

<div class="w-100"><br/></div>

@include("component.input._text", ["id"=>"place_of_birth", 'label'=>"محل تولد ", "value"=>$employment->worker->place_of_birth??"", "class_col"=>"","mark"=>"*"])
@include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
       "id"=>"date_of_birth",
       'label'=>"تاریخ تولد",
         'max_date'=>'today',
        "value"=>$employment->worker->date_of_birth??null,
        "class_col"=>"",
        "mark"=>"*"
        ])

