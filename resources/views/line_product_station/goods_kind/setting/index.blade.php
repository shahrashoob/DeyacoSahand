@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")



        @include("line_product_station.goods_kind.setting._tabs",["tab"=>$tab_index])


@endsection
@section("scripts")

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    @include("component.input.select2._script")
    <style>
        li {
            direction: rtl !important;
        }
    </style>
@endsection
