@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_personal])
    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_address])

    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$job_information_list])
{{--    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=> $educational_course_list])--}}
@endsection
