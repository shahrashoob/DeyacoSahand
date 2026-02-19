@extends('layouts.admin._master')
@section('page_header_title',"داشبورد طراحی کالا")
@section("content")



    @include("line_product_station.product.product_creation.product_show._tabs",["tab"=>"edit"])

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

@endsection

@section("scripts")
    @include("component.script_function.get_new_option")

    @include("line_product_station.product._init_info_script")
@endsection
