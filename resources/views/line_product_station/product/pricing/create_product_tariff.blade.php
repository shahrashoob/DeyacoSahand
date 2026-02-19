@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')

    @include("line_product_station.product.pricing._info_create")

@endsection

@section("styles")

    @include("component.input.select2._script")
@endsection
@section("scripts")

   <script>
       $('#form1').validate({
           rules: {
               "tariff_ids[]": "required",
               "packing_type_ids[]": "required",
               "degree_ids[]": "required",
               "type_of_sale_product_ids": "required",
               "warehouse_id": "required",
               "min_buy":{"required":true,"min":1},
               "max_buy":{"required":true,"min":1},
               "consumer_price":{"required":true,"min":0},
               "fea":{"required":true,"min":1},
               "tax":{"required":true,"min":0},
               "fare":{"required":true,"min":0},
           }
       });
   </script>
@endsection

