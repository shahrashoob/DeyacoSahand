@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>بارگذاری تصویر کالا </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("line_product_station.product.product_creation.product_image_quick.submit",$product_creation_process)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="  alert alert-info">لطفا تصویر نهایی کالا را بارگذاری نمایید.</div>
                            </div>
                            @include("component.input._file_upload",["id"=>"image_file","label"=>"تصویر نهایی کالا ( 300*300 پیکسل)","value"=>""])

                            <div class="col-md-12">
                                <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"
                                >بارگذاری تصویر
                                </button>
                            </div>
                        </div>


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
    @include("component.script_function.get_new_option")
    <script>
        $('#form1').validate({
            rules: {
                "image_file": "required",
            }
        });

    </script>
@endsection
