@extends('layouts.admin._master',["no_persian"=>1])
@section("page_header_title","داشبورد انبار ")
@section("content")

    <form id="form1" action="{{route("wh.warehouse_handling.new_handling.submit")}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>ایجاد انبارگردانی جدید
                        </h5>
                    </div>
                    <div class="card-block">

                        <div class="row">

                            <div class="col-md-3">
                                @include("component.input._aotocomplet2",[
                                   "id"=>"warehouse_id",
                                   "label"=>" انبار   ",
                                   "option"=>$warehouse_option["items"],
                                   "class_col"=>""
                               ])
                            </div>

                            @include("component.input._radio_box01",["id"=>"check_diff_in_amount",'label'=>"آیا مقدار کالا در انبارگردانی چک شود؟ ","value"=>0,"class_col"=>"col-md-3"])
{{--                            @include("component.input._radio_box01",["id"=>"check_diff_in_weight",'label'=>"آیا وزن خالص بسته بندی در انبارگردانی چک شود؟ ","value"=>0,"class_col"=>"col-md-3"])--}}
{{--                            @include("component.input._radio_box01",["id"=>"check_diff_in_sub_packing_form_number",'label'=>"آیا تعداد بسته بندی فرعی بسته بندی در انبارگردانی چک شود؟ ","value"=>0,"class_col"=>"col-md-3"])--}}
                            <div class="w-100"></div>
                            @include("component.input._text",["id"=>"max_diff_allowed","value"=>0,"label"=>"حداکثر اختلاف مجاز بین مقدار/وزن خالص انبارگردانی و مقدار/وزن خالص کالا","class_col"=>"col-md-3"])
                            <div class="w-100"></div>
                            @include("component.input._radio_box01",[
                          "id"=>"use_of_smart_object",
                          "label"=>"ثبت وزن/مقدار کالا به صورت",
                          "value"=>0,
                          "label0"=>"دستی",
                          "label1"=>"اشیاء هوشمند",
                          "class_col"=>"col-md-3 "
                          ])
                            <div class="w-100"><br/></div>



                            @include("component.input._radio_box01",["id"=>"all_product",'label'=>"آیا همه کالا های داخل انبار را انبارگردانی می کنید؟","value"=>1,"class_col"=>"col-md-3"])

                            <div class="col-md-9" data-select2-id="119">

                                @include("component.input.select2._select2",[
                               "id"=>"product_ids",
                               "label"=>" کالاهایی که در انبار گردانی وجود دارند ",
                               "option"=>$product_option["items"],
                               "class_col"=>""
                               ])
                            </div>



                            <div class="w-100"></div>

                                @include("component.input._radio_box01",["id"=>"breaking_by_classification",'label'=>"انتخاب همه کالاها به تفکیک طبقه بندی","value"=>0])

                            <div class="col-md-12">
                                در صورت انتخاب همه کالاها به تفکیک طبقه بندی، همه کالاهای داخل رسته کالایی برای انبار گردانی انتخاب می شوند و به ازای هر طبقه یک انبارگردانی ایجاد می گردد.
                            </div>

                            <div class="w-100"><br/></div>
                            <div class="col-md-3 classification_group" >
                                @include("component.input._select",[
                                    "id"=>"goods_kind_id",
                                    "label"=>"رسته کالایی  ",
                                    "option"=>$goods_kind_option["items"],
                                    "val"=>$goods_kind_option["value"],
                                    "text"=>$goods_kind_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"><br/></div>
                            <div class="col-md-3 classification_group" >
                                @include("component.input._select",[
                                    "id"=>"classification_id",
                                    "label"=>"طبقه بندی ",
                                     "option"=>$classification_option["items"],
                                    "val"=>$classification_option["value"],
                                    "text"=>$classification_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"><br/></div>


                            <div class="col-md-12">

                                <a href="{{route("wh.warehouse_handling.dashboard.index",)}}"
                                   class="btn btn-outline-dark">بازگشت</a>


                                <button type="submit" class="btn btn-primary"> افزودن</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
@section("styles")


    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

    <style>
        li {
            direction: rtl !important;
        }
    </style>
    @include("component.input.select2._script")
@endsection
@section("scripts")
    @include("component.script_function.get_new_option")
    <script>
        $('#form1').validate({
            rules: {
                "warehouse_id_auto": "required",
                "end_datetime_value": "required",
                "start_datetime_value": "required",
                "goods_kind_id": "required",
                "classification_id": "required",
                "product_ids[]":"required"

            }
        });

        $("#check_diff_in_amount_0,#check_diff_in_weight_0,#check_diff_in_sub_packing_form_number_0," +
            "#check_diff_in_amount_1,#check_diff_in_weight_1,#check_diff_in_sub_packing_form_number_1,"+
            "#all_product_0,#all_product_1"
            ).change(function (){
            update_input();
        })
        function update_input(){
            var display="none";

            if(
                $("input[name='check_diff_in_amount']:checked").val()=="1"
                // ||$("input[name='check_diff_in_weight']:checked").val()+0==1
                // ||$("input[name='check_diff_in_sub_packing_form_number']:checked").val()+0==1
            ){
                display="";
            }

            $("#use_of_smart_object_1").parent().css("display",display);
            $("#max_diff_allowed").parent().css("display",display);

            $("#product_ids").parent().css("display",$("input[name='all_product']:checked").val()=="1"?"none":"");
        }
        update_input();

        $("#breaking_by_classification_0,#breaking_by_classification_1").change(function (){
            classification();
        })
        $("#goods_kind_id").change(function () {

            get_new_option(
                0,
                $("#goods_kind_id").val(),
                " طبقه بندی کالایی",
                "classification_id",
                "goods_kind_classification_group_option",
            )
        })
        function classification(){
            if($("#breaking_by_classification_1").is(":checked")){
                $(".classification_group").css("display","")
            }
            else {
                $(".classification_group").css("display","none")
            }
        }
        classification();
    </script>
@endsection
