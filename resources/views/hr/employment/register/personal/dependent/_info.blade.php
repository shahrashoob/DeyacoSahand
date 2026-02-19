@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    @include("component.input._select", [
       "id"=>"dependent_type_id",
       "label"=>"نوع ارتباط با فرد",
       "option"=>$dependent_type_option["items"],
       "val"=>$dependent_type_option["value"],
       "text"=>$dependent_type_option["text"],
       "class_col"=>"",
       "mark"=>"*"
       ])
    <br/>

    @include("component.input._text", ["id"=>"first_name", 'label'=>"نام",    "value"=> $request["first_name"]??"", "class_col"=>"","mark"=>"*"])
    @include("component.input._text", ["id"=>"last_name", 'label'=>"نام خانوادگی",    "value"=> $request["last_name"]??"", "class_col"=>"","mark"=>"*"])

    @include("component.input._number", ["id"=>"national_code",
        'label'=>($employment->nationality_id==2 &&$employment->worker->user_address()->first()->address->country_id==112) ?"کد فراگیر" :"کدملی",
        "value"=>$request["national_code"]??"" ,
         "class_col"=>"","mark"=>"*"])

    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
           "id"=>"date_of_birth",
           'label'=>"تاریخ تولد",
             'max_date'=>'today',
            "value"=>$request["date_of_birth"] ??"",
            "class_col"=>"","mark"=>"*"])
    @if($post_document_receive_step_confirm && $employment->nationality_id==1 )
        <p class="alert-warning">تحویل مدارک زیر به بایگانی الزامی می باشد.لطفا اصل مدارک را در زمان تحویل به همراه داشته باشید.</p>
    @endif

    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_receive_step_document_type_list])

    <button class="btn btn-primary shadow-2 mb-4">ثبت</button>

@endsection

