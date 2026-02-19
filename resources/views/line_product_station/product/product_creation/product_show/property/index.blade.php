@extends('layouts.admin._master')
@section('page_header_title',"داشبورد طراحی کالا")
@section("content")


    @include("line_product_station.product.product_creation.product_show._tabs",["tab"=>"edit_property"])


@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        input {
            width: 70% !important;
        }
    </style>
@endsection

{{--@section("scripts")--}}
{{--   @include($view_path."_script")--}}
{{--@endsection--}}
