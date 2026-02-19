@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت اطلاعات BOM</h5>
                </div>
                <div class="card-block">
                    <div class="row">
                        @include("line_product_station.product.product_creation.basic_information_registration._info")
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row" id="bom_panel">
        @include("line_product_station.product.bom.bom._info")
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>

        #bom_panel .card-header {
            background: #dfe1f3 !important;
        }

        #bom_panel .multi-collapse {
            border: 5px solid #dfe1f3;
        }

        #bom_panel .accordion .card {
            margin-bottom: 5px;
        }
    </style>
@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",

            }
        });

    </script>
@endsection
