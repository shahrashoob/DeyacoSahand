@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")



    @include("line_product_station.product._tabs",["tab"=>"shade_number"])

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

