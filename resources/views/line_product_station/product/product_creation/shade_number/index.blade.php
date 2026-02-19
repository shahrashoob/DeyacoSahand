@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت شید های کالا</h5>
                </div>
                <div class="card-block">
                    <div class="row">
                        @include("line_product_station.product.product_creation.basic_information_registration._info")
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">

            @include("line_product_station.product.shade_number._info")

        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "code": "required",
            }
        });
    </script>
@endsection
