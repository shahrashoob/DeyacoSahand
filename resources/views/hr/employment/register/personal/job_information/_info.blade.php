@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    @include("component.input._text", ["id"=>"company_name_of_work", 'label'=>"نام شرکت",    "value"=>$request["company_name_of_work"]??"", "class_col"=>"","mark"=>"*"])
    @include("component.input._text", ["id"=>"post_caption", 'label'=>"پست سازمانی",  "value"=>$request["post_caption"]??"", "class_col"=>"","mark"=>"*"])


    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
    "id"=>"start_date_of_work",'max_date'=>'today', 'label'=>"تاریخ شروع کار", "value"=>$request["start_date_of_work"]??"",  "class_col"=>"","mark"=>"*"])

    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", ["id"=>"end_date_of_work",'max_date'=>'today', 'label'=>"تاریخ پایان کار", "value"=>$request["end_date_of_work"]??"",  "class_col"=>"","mark"=>"*"])

    @include("component.input._textarea", ["id"=>"address_of_work", 'label'=>"آدرس شرکت",    "value"=>$request["address_of_work"]??"", "class_col"=>"","mark"=>"*"])

    @include("component.input._text", ["id"=>"identifier_name", 'label'=>"نام تایید کننده",    "value"=>$request["identifier_name"]??"", "class_col"=>"","mark"=>"*"])

    @include("component.input._number", ["id"=>"identification_number", 'label'=>"شماره همراه تایید کننده","value"=>$request["identification_number"]??"" , "class_col"=>"","mark"=>"*"])

    @include("component.input._number", ["id"=>"insurance_number", 'label'=>"شماره بیمه","value"=>$employment->worker->insurance_number??"" , "class_col"=>""])


    @if($post_document_receive_step_confirm)
        <p class="alert-warning">تحویل مدارک زیر به بایگانی الزامی می باشد.لطفا اصل مدارک را در زمان تحویل به همراه داشته باشید.</p>
    @endif
    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_receive_step_document_type_list])
    <button class="btn btn-primary shadow-2 mb-4">ثبت </button>

@endsection

