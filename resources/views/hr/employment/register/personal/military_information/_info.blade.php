@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")


    @include("component.input._select", [
      "id"=>"military_information_id",
      "label"=>"وضعیت سربازی",
      "option"=>$military_option["items"],
      "val"=>$military_option["value"],
      "text"=>$military_option["text"],
      "class_col"=>"",
      "mark"=>"*"
      ])<br/>

    @include("hr.employment.register.personal_type._basic_document_type",["document_receive_step_document_type_list"=>$document_receive_step_document_type_list])

@endsection

