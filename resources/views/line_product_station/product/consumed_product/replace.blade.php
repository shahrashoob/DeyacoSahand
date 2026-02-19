@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")

    @include("line_product_station.product.consumed_product._info_replace")

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "new_material_id": "required",
            }
        });
    </script>
@endsection
