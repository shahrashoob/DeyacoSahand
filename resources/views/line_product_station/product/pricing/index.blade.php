@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")

    @include("line_product_station.product._tabs",["tab"=>"pricing"])

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "code": "required",
                @foreach($product_tariff_pricing as $item)
                "fea_{{$item->id}}": {"required":true,},
                "consumer_price_{{$item->id}}": {"required":true,},
                @endforeach
            }
        });
    </script>
@endsection

