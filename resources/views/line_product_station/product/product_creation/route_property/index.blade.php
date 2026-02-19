@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت مشخصات مسیر محصول </h5>
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
            <div class="card">
                <div class="card-header">
                    <h5><b>مشخصات مسیر محصول</b></h5>
                </div>
                <div class="card-block" id="content_step">
                    @include("line_product_station.product.route_property._info")
                </div>
            </div>
        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        #content_step input {
            width: 100px;
        }

        #content_step .card-header {
            background: #dfe1f3 !important;
        }

        #content_step .multi-collapse {
            border: 5px solid #dfe1f3;
        }

        #content_step .accordion .card {
            margin-bottom: 5px;
        }

        #content_step th {
            vertical-align: middle !important;
        }
    </style>
@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "min_inventory": "required",
            }
        });

    </script>
@endsection

