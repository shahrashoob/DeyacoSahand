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
                          action="{{route("line_product_station.product.product_creation.new_form.submit_from_exist_product")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                               <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"product_id",
                                    "label"=>" کالا ",
                                    "option"=>$product_option["items"],
                                    "val"=>$product_option["value"],
                                    "text"=>$product_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                               <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"status_id",
                                    "label"=>" وضعیت درخواست طراحی ",
                                    "option"=>$status_option["items"],
                                    "val"=>$status_option["value"],
                                    "text"=>$status_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>


                        </div>
                        <div class="w-100"><br/></div>

                        <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm('آیا از ثبت فرم طراحی کالا اطمینان دارید؟')">ایجاد طراحی کالا برای کالاهای موجود
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
                "caption": "required",
            }
        });


    </script>
    <script>


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
