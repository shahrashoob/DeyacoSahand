@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")




    @include("component.input._lable", ["label"=>"نام دوره",  "value"=>$user_educational_course->course_name,  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"نام موسسه",  "value"=>$user_educational_course->name_of_institution,  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>" تاریخ شروع دوره",  "value"=>$user_educational_course->get_start_date(),  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"تاریخ پابان دوره",  "value"=>$user_educational_course->get_end_date(),  "class_col"=>"col-md-12" ])
    @include("component.input._lable", ["label"=>"مدت دوره",  "value"=>$user_educational_course->duration,  "class_col"=>"col-md-12" ])

    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_receive_step_document_type_list])
    @if($employment->status_id==4640107)
        <a class="btn  mb-4" href="{{route("hr.employment.register.personal.confirm_upload_document.index",$employment->key)}}">بازگشت</a>
    @else

    <a class="btn  mb-4" href="{{route("hr.employment.register.personal.educational_course.index",$employment->key)}}">بازگشت</a>
    @endif
    <button class="btn btn-primary shadow-2 mb-4">بارگذاری </button>


@endsection

