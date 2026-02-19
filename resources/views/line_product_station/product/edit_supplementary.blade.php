@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")

    @include("line_product_station.product._tabs",["tab"=>"edit_supplementary"])

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

@endsection

@section("scripts")
    @include("line_product_station.product.init_info._frame_ratio_script")
    <script>
        $('#form1').validate({
            rules: {

                "unit_id": "required",
                "goods_type_id": "required",
                "frame_ratio_unit2": "required"
            }
        });
        frame_ratio_on_change();
        $("#sub_unit2_id").change(function () {

            frame_ratio_on_change();
        });
    </script>

@endsection
