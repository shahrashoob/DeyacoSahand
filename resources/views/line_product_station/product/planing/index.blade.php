@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")

    @include("line_product_station.product._tabs",["tab"=>"planing"])

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    @include("component.input.select2._script")
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "product_planing_algorithm_id": "required",
                "production_algorithm_type_id": "required",
                "order_point_algorithm_type_id": "required",
                "lidetime_algorithm_type_id": "required",
            }
        });


    </script>
@endsection
