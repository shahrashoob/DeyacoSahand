@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت کالاهای مصرفی (تعریف سریع کالای مشابه)</h5>
                </div>
                <div class="card-block">


                    <div class="row">

                        @include("line_product_station.product.product_creation.basic_information_registration._info")


                    </div>


                </div>
            </div>

        </div>

    </div>
    @include("line_product_station.product.consumed_product._info")

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

    @include("component.input.datepicker._script")
@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "amount": {
                    required: true,
                    max: {{$product_creation_process->product->goods_kind->max_number_for_sampling_production_card}}
                },
                "packing_type_id": "required",
                "max_delivery_datetime_value": "required"
            }
        });

    </script>
@endsection
