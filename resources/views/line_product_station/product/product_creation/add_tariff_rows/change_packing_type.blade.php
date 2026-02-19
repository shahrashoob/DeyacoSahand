@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><b>تغییر در بسته بندی های مجاز کالا </b></h5>
                </div>
                <div class="card-block">
                    @include("line_product_station.product.packing_type._info",["custom_route"=>"submit_change_packing_type"])
                </div>
            </div>
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
                "min_inventory": "required",
            }
        });

    </script>
@endsection
