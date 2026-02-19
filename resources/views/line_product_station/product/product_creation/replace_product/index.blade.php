@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت اطلاعات کالای جایگزین مصرف</h5>
                </div>
                <div class="card-block">
                    <div class="row">
                        @include("line_product_station.product.product_creation.basic_information_registration._info")
                    </div>
                </div>
            </div>
        </div>

    </div>

    @include("line_product_station.product.replace_product._info",["desable_insert_panel"=>1])


@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "replace_material_id_auto": "required",
                "ratio": "required",
            }
        });
        $('#form2').validate({
            rules: {
                "replace_product_id_auto": "required",
                "ratio": "required",
            }
        });

    </script>
@endsection
