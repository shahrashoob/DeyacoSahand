@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")

    @include("line_product_station.product._tabs",["tab"=>"waste"])

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
                "waste_during_consumption_id_1": "required",
                @foreach($product->route()->where("active_status_id",1200)->get() as $route)
                "waste_in_route_{{$route->id}}_1": "required",
                "waste_in_route_{{$route->id}}_1_percent": "required",
                @endforeach
            }
        });


    </script>
@endsection
