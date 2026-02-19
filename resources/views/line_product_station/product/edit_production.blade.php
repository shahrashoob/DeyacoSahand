@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")

    @include("line_product_station.product._tabs",["tab"=>"edit_production"])



@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "min_production": "required",
                "max_production": "required",
                "batch": "required",
                "min_buy": "required",
                "max_buy": "required",
                "batch_buy": "required",
                "extra_production": "required",
                "percent_of_waste": "required",
                "warehouse_id_auto": "required",
                "production_channel_id_auto": "required",
            }
        });
    </script>
@endsection
