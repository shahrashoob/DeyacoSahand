@extends('layouts.admin._master')
@section('page_header_title',"داشبورد طراحی کالا")
@section("content")



    @include("line_product_station.product.product_creation.product_show._tabs",["tab"=>"shade_number"])

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "code": "required",
            }
        });
    </script>
@endsection

