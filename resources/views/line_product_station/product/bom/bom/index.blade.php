@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")

    @include("line_product_station.product._tabs",["tab"=>"product_bom"])

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>

        .card-header{
            background: #dfe1f3 !important;
        }
        .multi-collapse{
            border:5px solid #dfe1f3;
        }
        .accordion .card{
            margin-bottom: 5px;
        }
    </style>
@endsection

@section("scripts")
    @include("component.script_function.get_new_option")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",

            }
        });


    </script>
@endsection
