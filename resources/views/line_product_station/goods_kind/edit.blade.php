@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش {{$goods_kind->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.goods_kind.update",$goods_kind)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان  ","value"=>$goods_kind->caption])
                            @include("component.input._text",["id"=>"caption_en",'label'=>"نام انگلیسی (نام پوشه کدهای خاص) جنس کالا  ","value"=>$goods_kind->caption_en])

                            @include("component.input._number",["id"=>"allowed_percentage_to_be_lower",'label'=>"درصد مجاز کمتر بودن مقدار تحویلی در زمان تحویل کالا از طرف انبار","value"=>$goods_kind->allowed_percentage_to_be_lower])
                            @include("component.input._number",["id"=>"allowed_percentage_to_be_higher",'label'=>"درصد مجاز بیشتر بودن مقدار تحویلی در زمان تحویل کالا از طرف انبار","value"=>$goods_kind->allowed_percentage_to_be_higher])
                            @include("component.input._number",["id"=>"allowed_percentage_in_lot_number_property",'label'=>"درصد مجاز اختلاف بین عدد وارد شده برای  مشخصه و مقدار محاسبه شده در سیستم به ازای هر لات","value"=>$goods_kind->allowed_percentage_in_lot_number_property])
                            @include("component.input._number",["id"=>"allowed_percentage_in_all_lot_number",'label'=>"درصد مجاز اختلاف بین عدد وارد شده برای وزن و مقدار محاسبه شده در همه لات ها","value"=>$goods_kind->allowed_percentage_in_all_lot_number])
                            @include("component.input._number",["id"=>"allowed_percentage_in_complete_form_information",'label'=>"درصد اختلاف (بین مقدار وارد شده توسط پیمانکار/تامین کننده و انبار) قابل قبول جهت تکمیل و ثبت اطلاعات در انبار",
"value"=>$goods_kind->allowed_percentage_in_complete_form_information])

                            @include("component.input._number",["id"=>"percent_check_of_quality_control_input_warehouse",'label'=>"درصد مجاز اختلاف بین گرماژ کالا در زمان کنترل تخلیه بار",
                           "value"=>$goods_kind->percent_check_of_quality_control_input_warehouse])

                            @include("component.input._number",["id"=>"number_check_of_quality_control_input_warehouse",'label'=>"تعداد مورد نیاز جهت تایید در زمان کنترل تخلیه بار",
                           "value"=>$goods_kind->number_check_of_quality_control_input_warehouse])

                            @include("component.input._number",["id"=>"be_lower_in_confirm_exit_form",'label'=>"درصد مجاز کمتر بودن مقدار تحویل جهت تایید فرم خروج از انبار","value"=>$goods_kind->be_lower_in_confirm_exit_form])

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"production_algorithm_type_id",
                                    "label"=>" روش برنامه ریزی تولید   ",
                                    "option"=>$production_algorithm_type_option["items"],
                                    "val"=>$production_algorithm_type_option["value"],
                                    "text"=>$production_algorithm_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            @include("component.input._number",["id"=>"max_number_for_sampling_production_card",'label'=>"حداکثر مقدار مجاز جهت ایجاد کارت نمونه گیری","value"=>$goods_kind->max_number_for_sampling_production_card])
                            @include("component.input._number",["id"=>"error_rate_in_checking_bom_weight",'label'=>"درصد خطای مجاز در زمان بررسی ناهنجاری داده ها در وزن BOM و وزن کالا","value"=>$goods_kind->error_rate_in_checking_bom_weight])


                            @include("component.input._number",["id"=>"min_diff_of_production_and_allocation_in_the_end_of_production",'label'=>"درصد مجاز کمتر بودن اختلاف کالای تولید شده با مقدار تخصیص کارت تولید/دستور پیمان در زمان پایان تولید","value"=>$goods_kind->min_diff_of_production_and_allocation_in_the_end_of_production])
                            @include("component.input._number",["id"=>"max_diff_of_production_and_allocation_in_the_end_of_production",'label'=>"درصد مجاز بیشتر بودن اختلاف کالای تولید شده با مقدار تخصیص کارت تولید/دستور پیمان در زمان پایان تولید","value"=>$goods_kind->max_diff_of_production_and_allocation_in_the_end_of_production])


                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"send_sms_in_create_allocation_machine_to_post_id1",
                                    "label"=>"ارسال پیامک تخصیص کارت تولید به پست های سازمانی",
                                    "option"=>$post_option["items"],
                                    "val"=>$post_option["value"],
                                    "text"=>$post_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"property1_id",
                                    "label"=>"اولین مشخصه مهم در رسته کالایی",
                                    "option"=>$property1_option["items"],
                                    "val"=>$property1_option["value"],
                                    "text"=>$property1_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"property2_id",
                                    "label"=>"دومین مشخه مهم در رسته کالایی",
                                    "option"=>$property2_option["items"],
                                    "val"=>$property2_option["value"],
                                    "text"=>$property2_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"property3_id",
                                    "label"=>"سومین مشخه مهم در رسته کالایی",
                                    "option"=>$property3_option["items"],
                                    "val"=>$property3_option["value"],
                                    "text"=>$property3_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"active_status_id",
                                    "label"=>" وضعیت   ",
                                    "option"=>$status_option["items"],
                                    "val"=>$goods_kind->active_status->id??"",
                                    "text"=>$goods_kind->active_status->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"required_parent_production",'label'=>"اجباری بودن ثبت سریال کارت تولید سطح بالا و بررسی قابلیت فروش کالا","checked"=>$goods_kind->required_parent_production])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"checking_compatibility_grade_in_delivery",'label'=>"بررسی انطباق درجه درخواست شده کالا از انبار با درجه تحویلی کالا","checked"=>$goods_kind->checking_compatibility_grade_in_delivery])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"possibility_of_issuing_a_production_manually",'label'=>"امکان صدور برگ دستور تولید به صورت دستی برای کالاهای رسته کالایی","checked"=>$goods_kind->possibility_of_issuing_a_production_manually])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"possibility_of_issuing_a_sample_production_manually",'label'=>"امکان صدور برگ دستور تولید نمونه گیری به صورت دستی برای کالاهای رسته کالای","checked"=>$goods_kind->possibility_of_issuing_a_sample_production_manually??0])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"checking_carrier_at_delivery_of_product",'label'=>" چک نمودن کد بسته بندی / حامل در زمان تایید تحویل کالا توسط انبار","checked"=>$goods_kind->checking_carrier_at_delivery_of_product??0])
                            </div>


                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"record_entry_into_warehouse_manually",'label'=>"ثبت ورود به انیار به صورت دستی توسط اپراتور","checked"=>$goods_kind->record_entry_into_warehouse_manually??0])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"record_out_of_warehouse_manually",'label'=>"ثبت خروج از انیار به صورت دستی توسط اپراتور","checked"=>$goods_kind->record_out_of_warehouse_manually??0])
                            </div>


                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"allow_show_sub_amount_in_exit_forms",'label'=>"آیا ستون واحد فرعی در برگ خروج ها نمایش داده شود","checked"=>$goods_kind->allow_show_sub_amount_in_exit_forms??0])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"allow_select_partial_of_packing_in_output",'label'=>"آیا امکان انتخاب بخشی از کالا در داشبورد خروج از کالا امکان پذیر است؟","checked"=>$goods_kind->allow_select_partial_of_packing_in_output??0])
                            </div>

                        </div>

                        <a href="{{route("line_product_station.goods_kind.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                    </form>

                </div>
            </div>
        </div>

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
                "caption": "required",
                "caption_en": "required",
                "production_algorithm_type_id_auto": "required",
                "allowed_percentage_in_lot_number_property": "required",
                "allowed_percentage_to_be_higher": "required",
                "allowed_percentage_to_be_lower": "required",
                "max_number_for_sampling_production_card": "required",
                "allowed_percentage_in_complete_form_information":"required"
            }
        });

    </script>
@endsection
