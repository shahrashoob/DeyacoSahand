@extends('customer.tmp.layouts.admin._master')
@section("page_header_title",__("user panel"))
@section("content")



    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>افزودن درخواست طراحی کالا</h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("customer_group.tmp.product_creation.submit")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            @include("component.input._hidden",["id"=>"product_creation_process_id",
                                        'label'=>"فرمی که برای تعریف کالای مصرفی ایجاد شده است. ",
                                        "value"=>$product_creation_process_id,

                                        ])

                            @include("component.input._text",["id"=>"caption",'label'=>__("input.suggested product name"),"value"=>"","class_col"=>"col-md-6"])
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"product_service_type_id",
                                    "label"=>__("input.product_service_type_id"),
                                    "option"=>$product_service_type_option["items"],
                                    "val"=>$product_service_type_option["value"],
                                    "text"=>$product_service_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"goods_kind_id",
                                    "label"=>__("input.goods_kind_id"),
                                    "option"=>$goods_kind_option["items"],
                                    "val"=>$product->goods_kind->id??"",
                                    "text"=>$product->goods_kind->caption??"",
                                    "class_col"=>"",

                                    ])
                            </div>

                            <div class="w-100"></div>
                            @include("component.input._file_upload",["id"=>"image_file","label"=>__("input.file upload for product creation"),"value"=>"","class_col"=>"col-md-6"])
                            <div class="col-md-12">
                                <br>

                                <b> {{__("input.has physical sample")}} </b>:
                                <input id="has_physical_sample_true" class="has_physical_sample"
                                       name="has_physical_sample" type="radio" value="1">{{__("input.yes")}}

                                <input class="has_physical_sample" name="has_physical_sample" type="radio" value="0">{{__("input.no")}}
                                <br>
                                <br>
                            </div>
                            <div class="col-md-12" id="method_of_sending_product" style="display: none">
                                <br>

                                <b> {{__("input.method of sending product")}}</b>:
                                <input name="method_of_sending_product_id" type="radio" checked="" value="1">
                                {{__("input.method of sending product in person")}}

                                <input name="method_of_sending_product_id" type="radio" value="2">
                                {{__("input.method of sending product in post service")}}
                                <br>
                                <br>
                            </div>
                        </div>

                        <a href="{{route("dashboard")}}"
                           class="btn btn-outline-dark">{{__("btn.back")}}</a>

                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm({{__("are you sure about the registration of the product design form")}})">
                            {{__("btn.add product creation")}}
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
                "has_physical_sample": "required",
                "method_of_sending_product_id": "required",
                "image_file": "required",
                "goods_kind_id": "required",
                "product_service_type_id": "required",
            }
        });

        $(".has_physical_sample").click(function () {

            if ($("#has_physical_sample_true").is(":checked")) {
                $("#method_of_sending_product").css("display", "");
            } else {
                $("#method_of_sending_product").css("display", "none");
            }
        })
        $("#product_service_type_id").change(function () {
            goods_kind();
        })

        function goods_kind() {

            if ($("#product_service_type_id").val() == 1) {
                $("#goods_kind_id").parent().css("display", "");
            }
            if ($("#product_service_type_id").val() == 2) {
                $("#goods_kind_id").parent().css("display", "none");
            }
        }

        goods_kind();
    </script>
@endsection
