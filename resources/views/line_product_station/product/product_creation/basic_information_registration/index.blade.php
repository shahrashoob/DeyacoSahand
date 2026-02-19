@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>تعریف اطلاعات پایه</h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("line_product_station.product.product_creation.basic_information_registration.submit",$product_creation_process)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("line_product_station.product.product_creation.basic_information_registration._info")

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"unit_id",
                                    "label"=>" واحد کالا  ",
                                    "option"=>$unit_option["items"],
                                    "val"=>$product->unit->id??"",
                                    "text"=>$product->unit->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"sub_unit_id",
                                    "label"=>" واحد فرعی کالا  ",
                                    "option"=>$sub_unit_option["items"],
                                    "val"=>$product->sub_unit->id??"",
                                    "text"=>$product->sub_unit->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"sub_unit2_id",
                                    "label"=>" واحد فرعی 2 کالا  ",
                                    "option"=>$sub_unit2_option["items"],
                                    "val"=>$product->sub_unit2->id??"",
                                    "text"=>$product->sub_unit2->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>
                            @include("component.input._number",["id"=>"frame_ratio_unit2",'label'=>"نسبت واحد فرعی 2 به واحد اصلی","value"=>$product->frame_ratio_unit2])

                            @include("component.input._number",["id"=>"number_in_carton",'label'=>"تعداد در واحد اصلی ( ویژه انتقال به نوسا)","value"=>$product->number_in_carton])
                            @include("component.input._number",["id"=>"predictive_weight",'label'=>"وزن پیش بینی کالا","value"=>$product->predictive_weight])

                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"goods_type_id",
                                    "label"=>" نوع کالا",
                                    "option"=>$goods_type_option["items"],
                                    "val"=>$product->goods_type->id??"",
                                    "text"=>$product->goods_type->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"supply_type_id",
                                    "label"=>" نوع تامین   ",
                                    "option"=>$supply_type_option["items"],
                                    "val"=>$product->supply_type->id??"",
                                    "text"=>$product->supply_type->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>

                                <div class="w-100"><br/></div>

                                <div class="col-md-6">
                                    @include("component.input._select",[
                                        "id"=>"service_id_in_employer_system",
                                        "label"=>"  کد خدمت در سامانه کارفرما ",
                                        "option"=>$service_id_in_employer_system_option["items"],
                                        "val"=>$service_id_in_employer_system_option["value"],
                                        "text"=>$service_id_in_employer_system_option["text"],
                                        "class_col"=>""
                                        ])
                                </div>
                            <div class="w-100"><br/></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"unit_of_measure_type_id_in_production",
                                    "label"=>" ترتیب اهمیت واحد های کالا در ماشین  ",
                                    "option"=>$unit_of_measure_type_in_production_option["items"],
                                    "val"=>$unit_of_measure_type_in_production_option["value"],
                                    "text"=>$unit_of_measure_type_in_production_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"><br/></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"unit_of_measure_type_id_in_sale",
                                    "label"=>" ترتیب اهمیت واحد های کالا در فروش  ",
                                    "option"=>$unit_of_measure_type_in_sale_option["items"],
                                    "val"=>$unit_of_measure_type_in_sale_option["value"],
                                    "text"=>$unit_of_measure_type_in_sale_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                        </div>

                        <br/>
                        <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary">ثبت اطلاعات
                        </button>

                        <a href="{{route("line_product_station.product.product_creation.basic_information_registration.copy_form_other",$product_creation_process)}}" class="btn btn-primary">کپی کالا
                        </a>

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
{{--    @include("line_product_station.product.init_info._frame_ratio_script")--}}

    <script>
        function frame_ratio_on_change(){

            if($("#sub_unit2_id").val() == 1400){ // قاب
                $("#frame_ratio_unit2").parent().css("display","");
            }
            else{
                $("#frame_ratio_unit2").parent().css("display","none");
            }

        }
        $('#form1').validate({
            rules: {

                "unit_id": "required",
                // "sub_unit_id": "required",
                // "sub_unit2_id": "required",
                "number_in_carton": "required",
                "supply_type_id": "required",
                "predictive_weight": "required",
                "service_id_in_employer_system": "required",
                "frame_ratio_unit2": "required",
            }
        });


        function service_id_in_employer_system() {
            if ($("#supply_type_id").val() == 3) {
                $("#service_id_in_employer_system").parent().css("display", "")
            } else {
                $("#service_id_in_employer_system").parent().css("display", "none")
            }
        }
        service_id_in_employer_system();
        $("#supply_type_id").change(function (){
            service_id_in_employer_system();
        })
        frame_ratio_on_change();
        $("#sub_unit2_id").change(function () {

            frame_ratio_on_change();
        });

    </script>
@endsection
