@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")



    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_address])
    <a class="btn  mb-4" href="{{route("hr.employment.register.personal.confirm_upload_document.index",$employment->key)}}">بازگشت</a>
    <button class="btn btn-primary shadow-2 mb-4">بارگزاری </button>


@endsection

