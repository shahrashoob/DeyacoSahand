@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت خدمت در نرم افزار مالی </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("line_product_station.product.product_creation.register_service_in_financial_software.submit",$product_creation_process)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="  alert alert-info">لطفا پست از تعریف خدمت در نرم افزار مالی، کد و نام کالا را در کادر زیر وارد نمایید. </div>
                            </div>
                            @include("component.input._text",["id"=>"code",'label'=>"کد خدمت ","value"=>""])
                            @include("component.input._text",["id"=>"caption",'label'=>"نام خدمت ","value"=>""])

                            <div class="col-md-12">
                                <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"
                                >تایید اطلاعات خدمت
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
                "code": "required",
                "caption": "required",
            }
        });

    </script>
@endsection
