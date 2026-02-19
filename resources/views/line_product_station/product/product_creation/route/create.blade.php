@extends('layouts.admin._master')

@section("page_header_title"," داشبورد طراحی کالا ")

@section('content')

    @include("line_product_station.product.route._info_create")

@endsection

@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "code": "required",
                "active_status_id_auto": "required",
            }
        });
    </script>
@endsection

