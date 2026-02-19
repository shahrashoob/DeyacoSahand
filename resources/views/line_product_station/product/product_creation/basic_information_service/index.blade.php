@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>تکمیل اطلاعات خدمت</h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("line_product_station.product.product_creation.basic_information_service.submit",$product_creation_process)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            @include("line_product_station.product.product_creation.basic_information_service._info_service")
                        </div><br/>
                            <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary">ثبت
                            </button>

                    </form>

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
                "unit_id2": "required",
                "number_in_carton2": "required",
                "supply_type_id2": "required",
                "product_service_type_id2": "required",
            }
        });
    </script>
@endsection