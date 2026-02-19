@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")



    @include("line_product_station.product._tabs",["tab"=>"edit_lot_number"])

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "code": "required",
            }
        });
        $('#form2').validate({
            rules: {
                "replace_product_id_auto": "required",
                "ratio": "required",
            }
        });
    </script>
@endsection

