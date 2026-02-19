@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    @include("component.input._lable", ["label"=>"پست سازمانی",  "value"=>$user_job_information->post_caption,  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"نام شرکت",  "value"=>$user_job_information->company_name_of_work,  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"تاریخ شروع کار",  "value"=>$user_job_information->get_start_date_of_work(),  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"تاریخ پایان کار",  "value"=>$user_job_information->get_end_date_of_work(),  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"نام معرف",  "value"=>$user_job_information->identifier_name,  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"شماره معرف",  "value"=>$user_job_information->identification_number,  "class_col"=>"col-md-12" ])

    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_receive_step_document_type_list])
    @if($employment->status_id==4640107)
        <a class="btn  mb-4" href="{{route("hr.employment.register.personal.confirm_upload_document.index",$employment->key)}}">بازگشت</a>
    @else

        <a class="btn  mb-4"
           href="{{route("hr.employment.register.personal.job_information.index",$employment->key)}}">بازگشت</a>
    @endif
    <button class="btn btn-primary shadow-2 mb-4">بارگذاری</button>

@endsection

