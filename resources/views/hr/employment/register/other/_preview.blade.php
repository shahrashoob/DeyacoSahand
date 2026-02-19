@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")
    @include("component.input._lable", ["id"=>"description", 'label'=>"توضیحات بیشتر",    "value"=>$employment->description??"", "class_col"=>""])
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection