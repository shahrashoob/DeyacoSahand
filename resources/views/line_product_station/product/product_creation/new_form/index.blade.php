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

                    <form id="form1"
                          action="{{route("line_product_station.product.product_creation.new_form.submit")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            @include("component.input._hidden",["id"=>"product_creation_process_id",
                                        'label'=>"فرمی که برای تعریف کالای مصرفی ایجاد شده است. ",
                                        "value"=>$product_creation_process_id])

                            @include("component.input._text",["id"=>"caption",'label'=>"نام پیشنهادی کالا / خدمت ","value"=>""])
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"product_service_type_id",
                                    "label"=>" نوع کالا / خدمت  ",
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
                                    "label"=>"رسته کالا",
                                    "option"=>$goods_kind_option["items"],
                                    "val"=>$product->goods_kind->id??"",
                                    "text"=>$product->goods_kind->caption??"",
                                    "class_col"=>"",

                                    ])
                            </div>

                            @include("component.input._radio_box01",["id"=>"has_sampling_required",
                                                          "label"=>"آیا نیاز به تایید نمونه کالا می باشد؟",
                                                          "label0"=>"خیر",
                                                          "label1"=>"بله",
                                                          "value"=>"",
                                                          ])


                            @include("component.input._file_upload",["id"=>"image_file","label"=>"تصویر نمونه کالا ( 300*300 پیکسل)","value"=>""])
                            <div class="col-md-12">
                                <br>

                                <b> آیا می خواهید نمونه کالا را به کارخانه ارسال کنید؟ </b>:
                                <input id="has_physical_sample_true" class="has_physical_sample"
                                       name="has_physical_sample" type="radio" value="1">بله

                                <input class="has_physical_sample" name="has_physical_sample" type="radio" value="0">خیر
                                <br>
                                <br>
                            </div>
                            <div class="col-md-12" id="method_of_sending_product" style="display: none">
                                <br>

                                <b> روش تحویل نمونه کالا </b>:
                                <input name="method_of_sending_product_id" type="radio" checked="" value="1"> تحویل به
                                صورت حضوری

                                <input name="method_of_sending_product_id" type="radio" value="2"> خدمات پستی
                                <br>
                                <br>
                            </div>
                        </div>

                        <br>
                        <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm('آیا از ثبت فرم طراحی کالا اطمینان دارید؟')">ثبت درخواست طراحی جدید
                        </button>


                        <a href="{{route("line_product_station.product.product_creation.new_form.create_from_exist_product")}}"
                           class="btn btn-outline-primary">ایجاد طراحی کالا برای کالاهای موجود</a>



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
                $("#has_physical_sample_true").parent().css("display", "");
                $("#has_sampling_required_1").parent().css("display", "");
                $("#image_file").parent().css("display", "");

            }
            if ($("#product_service_type_id").val() == 2) {
                $("#goods_kind_id").parent().css("display", "none");
                $("#has_physical_sample_true").parent().css("display", "none");
                $("#has_sampling_required_1").parent().css("display", "none");
                $("#image_file").parent().parent().css("display", "none");

            }
        }

        goods_kind();
    </script>
    <script>
        var good_kind_with_sampling = [];
        @foreach($good_kind_with_sampling as $key => $value)
            good_kind_with_sampling[{{$key}}] = {{$value}};
        @endforeach

        $("#goods_kind_id").change(function () {
            updateSamplingRequired();
        });

        function updateSamplingRequired() {

            var selectedGoodKindId = $("#goods_kind_id").val();
            var samplingRequired = good_kind_with_sampling[selectedGoodKindId];
            if (samplingRequired == 1) {
                $("#has_sampling_required_0").parent().show();

            } else {
                $("#has_sampling_required_0").parent().hide();

            }
        }

        // updateSamplingRequired();
    </script>

@endsection
