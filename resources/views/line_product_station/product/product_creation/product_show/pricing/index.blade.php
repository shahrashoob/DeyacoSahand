@extends('layouts.admin._master')
@section('page_header_title',"داشبورد طراحی کالا")
@section("content")

    @include("line_product_station.product.product_creation.product_show._tabs",["tab"=>"pricing"])

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "code": "required",
                @foreach($product_tariff_pricing as $item)
                "fea_{{$item->id}}": {"required":true,"min":1},
                "consumer_price_{{$item->id}}": {"required":true,"min":1},
                @endforeach
            }
        });
    </script>
@endsection

