@include("component.input._text", ["id"=>"email", 'label'=>$employment->personal_type_id ==1?"نام کاربری":"نام کاربری مدیر عامل", "value"=>$email??"", "class_col"=>"","mark"=>"(باید شامل حروف انگلیسی باشد) *"])


@include("component.input._text", ["id"=>"firstname", 'label'=>$employment->personal_type_id ==1?"نام ":"نام مدیر عامل", "value"=>$employment->worker->firstname??"", "class_col"=>"","mark"=>"*"])
@include("component.input._text", ["id"=>"lastname", 'label'=>$employment->personal_type_id == 1?"نام خانوادگی ":"نام خانوادگی مدیر عامل", "value"=>$employment->worker->lastname??"", "class_col"=>"","mark"=>"*"])

{{--@if($employment->personal_type_id == 1)--}}
{{--    @include("component.input._text", ["id"=>"father_name", 'label'=>"نام پدر", "value"=>$employment->worker->father_name??"", "class_col"=>"","mark"=>"*"])--}}
{{--    @include("component.input._number", ["id"=>"birth_certificate_number", 'label'=> $employment->get_caption_of_birth_certificate_number($employment->nationality_id), "value"=>$employment->worker->birth_certificate_number??"", "class_col"=>"","mark"=>"*"])--}}
{{--@endif--}}

@if($employment->personal_type_id == 2)
    @include("component.input._text", ["id"=>"caption", 'label'=>"نام شرکت", "value"=>$employment->company->caption??"", "class_col"=>"","mark"=>"*"])
    @include("component.input._number", ["id"=>"register_code", 'label'=>"شماره ثبت", "value"=>$employment->company->register_code??"", "class_col"=>"","mark"=>"*"])
    @include("component.input._number", ["id"=>"national_code", 'label'=>"کد ملی مدیر عامل", "value"=>$employment->worker->national_code??"", "class_col"=>"","mark"=>"*"])
@endif


@include("component.input._select", [
                  "id"=>"gender_id",
                  "label"=>($employment->personal_type_id == 2)?"جنسیت مدیرعامل":"جنسیت",
                  "option"=>$gender_option["items"],
                  "val"=>$gender_option["value"],
                  "text"=>$gender_option["text"],
                  "class_col"=>"","mark"=>"*",
                  ])
{{--@if($employment->personal_type_id == 1)--}}
{{--    <div class="w-100"><br/></div>--}}
{{--    @include("component.input._text", ["id"=>"place_of_birth", 'label'=>"شهر محل تولد ", "value"=>$employment->worker->place_of_birth??"", "class_col"=>"","mark"=>"*"])--}}
{{--@endif--}}

@include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
       "id"=>"date_of_birth",
       'label'=>"تاریخ تولد",
         'max_date'=>'today',
        "value"=>$employment->worker->date_of_birth??null,
        "class_col"=>"","mark"=>"*",])
@if($get_the_contractor_image)
    @include("component.input._file_upload", ["id"=>"user_image_file_id", 'label'=>"تصویر/لوگو","value"=> "", "class_col"=>"","mark"=>"*"])
@endif
