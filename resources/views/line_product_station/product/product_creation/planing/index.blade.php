@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت تنظمات برنامه ریزی</h5>
                </div>
                <div class="card-block">

                        @include("line_product_station.product.planing._info")

                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    @include("component.input.select2._script")
@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "product_planing_algorithm_id": "required",
                "production_algorithm_type_id": "required",
                "order_point_algorithm_type_id": "required",
                "lidetime_algorithm_type_id": "required",

            }
        });

    </script>
@endsection
