@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")



    @include("line_product_station.product._tabs",["tab"=>"sales"])

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "type_of_sale_product_id": "required",
                "service_id": "required",
            }
        });
    </script>
@endsection

