@extends('layouts.admin._master')
@section('page_header_title',"داشبورد طراحی کالا")
@section("content")

    @include("line_product_station.product.product_creation.product_show._tabs",["tab"=>"route_property"])

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>

        input {
            width: 100px;
        }

        .card-header {
            background: #dfe1f3 !important;
        }

        .multi-collapse {
            border: 5px solid #dfe1f3;
        }

        .accordion .card {
            margin-bottom: 5px;
        }

        th {
            vertical-align: middle !important;
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
