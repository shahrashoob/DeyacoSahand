@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")




    @include("component.input._text", ["id"=>"course_name", 'label'=>"نام دوره",  "value"=>$request["course_name"]??"", "class_col"=>"","mark"=>"*"])

    @include("component.input._text", ["id"=>"name_of_institution", 'label'=>"نام موسسه",    "value"=>$request["name_of_institution"]??"", "class_col"=>"","mark"=>"*"])

    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", ["id"=>"start_date",'max_date'=>'today', 'label'=>"تاریخ شروع دوره", "value"=>$request["start_date"]??"",  "class_col"=>"","mark"=>"*"])

    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", ["id"=>"end_date",'max_date'=>'today', 'label'=>"تاریخ پایان دوره", "value"=>$request["end_date"]??"",  "class_col"=>"","mark"=>"*"])

    @include("component.input._number", ["id"=>"duration", 'label'=>"مدت دوره(ساعت)",    "value"=>$request["duration"]??"", "class_col"=>"","mark"=>"*"])

    @if($post_document_receive_step_confirm)
        <p class="alert-warning">تحویل مدارک زیر به بایگانی الزامی می باشد.لطفا اصل مدارک را در زمان تحویل به همراه داشته باشید.</p>
    @endif
    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_receive_step_document_type_list])
    <button class="btn btn-primary shadow-2 mb-4">ثبت </button>
@endsection
