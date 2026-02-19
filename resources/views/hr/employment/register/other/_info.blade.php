@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")
    @include("component.input._textarea", ["id"=>"description", 'label'=>"توضیحات بیشتر",    "value"=>$employment->description??"", "class_col"=>""])
@endsection
