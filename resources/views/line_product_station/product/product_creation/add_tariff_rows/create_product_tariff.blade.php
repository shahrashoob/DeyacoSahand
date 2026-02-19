@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>افزودن کالا به لیست تعرفه</h5>
                </div>
                <div class="card-block">
                    <div class="row">
                        @include("line_product_station.product.pricing._info_create")
                    </div>
                </div>
            </div>
        </div>

    </div>


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

