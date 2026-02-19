@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تنظیمات روش محاسبه مقدار واحدهای خروجی {{$machine_type->caption}} در رسته کالایی  {{$machine_type_output_band_goods_kind->goods_kind->caption}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("line_product_station.machine_type.output_band.submit_calculation_method",[$machine_type,$machine_type_output_band,$machine_type_output_band_goods_kind])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"machine_type_calculation_method_for_unit_id",
                                    "label"=>"روش  محاسبه مقدار اصلی کالا در رسته کالایی",
                                    "option"=>$machine_type_calculation_method_for_unit_option["items"],
                                    "text"=>$machine_type_calculation_method_for_unit_option["text"],
                                    "val"=>$machine_type_calculation_method_for_unit_option["value"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="col-md-6">

                                @include("component.input._select",[
                                    "id"=>"smart_object_id_for_unit",
                                    "label"=>"انتخاب اشیاء",
                                    "option"=>$smart_object_for_unit_option["items"],
                                    "text"=>$smart_object_for_unit_option["text"],
                                    "val"=>$smart_object_for_unit_option["value"],
                                    "class_col"=>""
                                    ])

                            </div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"machine_type_calculation_method_for_sub_unit_id",
                                    "label"=>"روش محاسبه مقدار فرعی کالا در رسته کالایی",
                                    "option"=>$machine_type_calculation_method_for_sub_unit_option["items"],
                                    "text"=>$machine_type_calculation_method_for_sub_unit_option["text"],
                                    "val"=>$machine_type_calculation_method_for_sub_unit_option["value"],
                                    "class_col"=>""
                                    ])
                            </div>


                            <div class="col-md-6">

                                @include("component.input._select",[
                                    "id"=>"smart_object_id_for_sub_unit",
                                    "label"=>"انتخاب اشیاء",
                                    "option"=>$smart_object_for_sub_unit_option["items"],
                                    "text"=>$smart_object_for_sub_unit_option["text"],
                                    "val"=>$smart_object_for_sub_unit_option["value"],
                                    "class_col"=>""
                                    ])

                            </div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"machine_type_calculation_method_for_sub_unit2_id",
                                    "label"=>"روش محاسبه مقدار فرعی 2 کالا در رسته کالایی",
                                    "option"=>$machine_type_calculation_method_for_sub_unit2_option["items"],
                                    "text"=>$machine_type_calculation_method_for_sub_unit2_option["text"],
                                    "val"=>$machine_type_calculation_method_for_sub_unit2_option["value"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="col-md-6">

                                @include("component.input._select",[
                                    "id"=>"smart_object_id_for_sub_unit2",
                                    "label"=>"انتخاب اشیاء",
                                    "option"=>$smart_object_for_sub_unit2_option["items"],
                                    "text"=>$smart_object_for_sub_unit2_option["text"],
                                    "val"=>$smart_object_for_sub_unit2_option["value"],
                                    "class_col"=>""
                                    ])

                            </div>

                            <div class="col-md-12">
                                <br/>
                                <a href="{{route("line_product_station.machine_type.output_band.index",$machine_type)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"> ثبت روش های محاسبه</button>
                            </div>
                        </div>




                    </form>

                </div>
            </div>
        </div>

       @include("line_product_station.machine_type.output_band._warehouse")

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "goods_kind_id_auto": "required",
                "line_output_number": "required",
                "smart_object_id_for_unit": "required",
                "smart_object_id_for_sub_unit": "required",
                "smart_object_id_for_sub_unit2": "required",
            }
        });

        function update() {

            if ($("#machine_type_calculation_method_for_unit_id").val() == 2) {
                $("#smart_object_id_for_unit").parent().css("display", "block");
            } else {
                $("#smart_object_id_for_unit").parent().css("display", "none");
            }
            if ($("#machine_type_calculation_method_for_sub_unit_id").val() == 2) {
                $("#smart_object_id_for_sub_unit").parent().css("display", "block");
            } else {
                $("#smart_object_id_for_sub_unit").parent().css("display", "none");
            }
            if ($("#machine_type_calculation_method_for_sub_unit2_id").val() == 2) {
                $("#smart_object_id_for_sub_unit2").parent().css("display", "block");
            } else {
                $("#smart_object_id_for_sub_unit2").parent().css("display", "none");
            }
        }

        $("#machine_type_calculation_method_for_unit_id").change(function () {
            update();

        });
        $("#machine_type_calculation_method_for_sub_unit_id").change(function () {
            update();

        });
        $("#machine_type_calculation_method_for_sub_unit2_id").change(function () {
            update();

        });
        update();
    </script>
@endsection
