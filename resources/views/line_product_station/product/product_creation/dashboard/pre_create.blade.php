@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")



    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>افزودن درخواست طراحی کالا</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.product.creation_process.dashboard.store")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">


                            @include("component.input._hidden",["id"=>"caption","value"=>$product_caption])

                            @include("component.input._lable",["id"=>"caption",'label'=>"درخواست دهنده ","value"=>$worker->fullName()])

                            @include("component.input._lable",["id"=>"caption",'label'=>"نام پیشنهادی کالا ","value"=>$product_caption])

                            @if($customer)
                                @include("component.input._lable",["id"=>"caption",'label'=>"آدرس فرستنده ","value"=>$customer->getDefaultAddress()->address??""])
                                @include("component.input._lable",["id"=>"caption",'label'=>"تلفن فرستنده ","value"=>$customer->getDefaultAddress()->phone??""])
                            @endif

                            @include("component.input._lable",["id"=>"caption",'label'=>"آدرس گیرنده ","value"=>$product_creation_unit_address])
                            @include("component.input._lable",["id"=>"caption",'label'=>"تلفن گیرنده ","value"=>$product_creation_unit_phone])
                            <div class="alert alert-info col-md-12">
                                در صورت تایید می بایست نمونه پارچه را به واحد طراحی تحویل/ارسال نمایید،
                            </div>
                        </div>


                        <a href="{{route("line_product_station.product.creation_process.dashboard.create")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm(' آیا از تایید فرم اطمینان دارید؟')">تایید و ثبت درخواست
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
    @include("component.script_function.get_new_option")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "code": "required",
                "status_id_auto": "required",
                "unit_id_auto": "required",
                "goods_kind_id_auto": "required",
                "goods_type_id_auto": "required",
                "number_in_carton": "required",
                "weight": "required",
                "ic": "required",
                "image_file": "required",
            }
        });
        $("#goods_kind_id").change(function () {
            get_new_option(
                $("#product_type_id").val(),
                $("#goods_kind_id").val(),
                "گروه کالایی",
                "product_type_id",
                "product_type"
            )
        })
    </script>
@endsection
