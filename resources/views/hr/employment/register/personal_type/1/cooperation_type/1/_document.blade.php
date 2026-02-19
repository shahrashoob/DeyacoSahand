@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")



    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_personal])
    @if($employment->status_id==4640107)
        <a class="btn  mb-4"
           href="{{route("hr.employment.register.personal.confirm_upload_document.index",$employment->key)}}">بازگشت</a>
    @else
    <a class="btn  mb-4" href="{{route("hr.employment.register.personal.confirm_upload_document.index",$employment->key)}}">بازگشت</a>
    @endif
    <button class="btn btn-primary shadow-2 mb-4">بارگذاری </button>


@endsection

