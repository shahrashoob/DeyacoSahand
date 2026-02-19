<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
    "id"=>"line_id",
    "label"=>" خط تولید ",
    "option"=>$line_option["items"],
    "val"=>$line_option["value"],
    "text"=>$line_option["text"],
    "class_col"=>""
    ])
</div>

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
    "id"=>"station_id",
    "label"=>" ایستگاه کاری ",
    "option"=>$station_option["items"],
    "val"=>$station_option["value"],
    "text"=>$station_option["text"],
    "class_col"=>""
    ])
</div>

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
    "id"=>"station_operation_id",
    "label"=>" عملیات در ایستگاه کاری ",
    "option"=>$station_operation_option["items"],
    "val"=>$station_operation_option["value"],
    "text"=>$station_operation_option["text"],
    "class_col"=>""
    ])
</div>

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
    "id"=>"station_sub_operation_id",
    "label"=>" عملیات فرعی ",
    "option"=>$station_sub_operation_option["items"],
    "val"=>$station_sub_operation_option["value"],
    "text"=>$station_sub_operation_option["text"],
    "class_col"=>""
    ])
</div>

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
    "id"=>"machine_type_id",
    "label"=>" گروه ماشین ",
    "option"=>$machine_type_option["items"],
    "val"=>$machine_type_option["value"],
    "text"=>$machine_type_option["text"],
    "class_col"=>""
    ])
</div>


@include("component.input._number",["id"=>"practical_capacity_of_production",'label'=>("ظرفیت عملی تولید (".$product->unit->caption." در ساعت)"),"value"=>$line_product_station->practical_capacity_of_production??""])
@include("component.input._number",["id"=>"efficiency",'label'=>"کارایی شاخص خرد","value"=>$line_product_station->efficiency??""])
@include("component.input._number",["id"=>"priority_number",'label'=>"اولویت","value"=>$line_product_station->priority_number??""])


@include("component.input._number",["id"=>"min_of_production",'label'=>"حداقل تولید","value"=>$line_product_station->min_of_production??""])
@include("component.input._number",["id"=>"max_of_production",'label'=>"حداکثر تولید  ","value"=>$line_product_station->max_of_production??""])
@include("component.input._number",["id"=>"extra_production",'label'=>"تعداد اضافه تولید","value"=>$line_product_station->extra_production??""])
@include("component.input._number",["id"=>"percent_of_extra_production",'label'=>"درصد اضافه تولید ","value"=>$line_product_station->percent_of_extra_production??""])

@include("component.input._number",["id"=>"batch",'label'=>"بچ تولید","value"=>$line_product_station->batch??""])
@include("component.input._number",["id"=>"batch_error_percentage",'label'=>"درصد خطای بچ","value"=>$line_product_station->batch_error_percentage??""])

<div class="col-md-6">
    @include("component.input._select",[
    "id"=>"material_id_dependent_to_batch",
    "label"=>"مواد اولیه وابسته به بچ",
    "option"=>$material_id_dependent_to_batch_option["items"],
    "val"=>$material_id_dependent_to_batch_option["value"],
    "text"=>$material_id_dependent_to_batch_option["text"],
    "class_col"=>""
    ])
</div>
<div class="w-100"><br/></div>
<div class="col-md-6">
    @include("component.input._select",[
    "id"=>"material_unit_type_id_dependent_to_batch",
    "label"=>"واحد مواد اولیه وابسته به بچ",
    "option"=>$material_unit_type_id_dependent_to_batch_option["items"],
    "val"=>$material_unit_type_id_dependent_to_batch_option["value"],
    "text"=>$material_unit_type_id_dependent_to_batch_option["text"],
    "class_col"=>""
    ])
</div>
<div class="w-100"><br/></div>
<div class="col-md-6">
    @include("component.input._select",[
    "id"=>"material_packing_type_id_dependent_to_batch",
    "label"=>"بسته بندی مجاز مواد اولیه وابسته به بچ",
    "option"=>$material_packing_type_id_dependent_to_batch_option["items"],
    "val"=>$material_packing_type_id_dependent_to_batch_option["value"],
    "text"=>$material_packing_type_id_dependent_to_batch_option["text"],
    "class_col"=>""
    ])
</div>
<div class="w-100"><br/></div>
{{--@include("component.input._number",["id"=>"setup_time",'label'=>"مدت زمان ستاپ (دقیقه)","value"=>$line_product_station->setup_time??""])--}}
@include("component.input._number",["id"=>"setup_time_for_sub_operation",'label'=>"مدت زمان عملیات فرعی","value"=>$line_product_station->setup_time_for_sub_operation??""])


<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
    "id"=>"production_channel_type_id",
    "label"=>" نوع کانال تولید ",
    "option"=>$production_channel_type_option["items"],
    "val"=>$production_channel_type_option["value"],
    "text"=>$production_channel_type_option["text"],
    "class_col"=>""
    ])
</div>

@include("component.input._radio_box01",[
    "id"=>"is_need_allocation_at_first","label"=>"آیا در ابتدا نیاز به تخصیص دارد؟","label0"=>"خیر","label1"=>"بله",
"value"=>$line_product_station->is_need_allocation_at_first??0])

@include("component.input._radio_box01",[
    "id"=>"is_need_start_setup","label"=>"آیا نیاز به شروع ستاپ (setup) دارد؟","label0"=>"خیر","label1"=>"بله",
"value"=>$line_product_station->is_need_start_setup??0])

@include("component.input._radio_box01",[
    "id"=>"is_need_start_of_operation","label"=>"آیا نیاز به شروع عملیات دارد؟","label0"=>"خیر","label1"=>"بله",
"value"=>$line_product_station->is_need_start_of_operation??0])

@include("component.input._radio_box01",[
    "id"=>"is_need_end_of_operation","label"=>"آیا نیاز به پایان عملیات دارد؟","label0"=>"خیر","label1"=>"بله",
"value"=>$line_product_station->is_need_end_of_operation??0])

@include("component.input._radio_box01",[
    "id"=>"is_need_final_setting","label"=>"آیا نیاز به انجام تنظیمات نهایی دارد؟","label0"=>"خیر","label1"=>"بله",
"value"=>$line_product_station->is_need_final_setting??0])

@include("component.input._radio_box01",[
    "id"=>"is_need_for_quality_control","label"=>"آیا بعد از انجام عملیات
نیاز به تایید کنترل کیفیت می باشد؟","label0"=>"خیر","label1"=>"بله",
"value"=>$line_product_station->is_need_for_quality_control??0])

@include("component.input._radio_box01",[
    "id"=>"is_ability_to_choose_next_station","label"=>"آیا امکان انتخاب ایستگاه بعدی (در صورت عدم تایید کنترل کیفیت) دارد؟","label0"=>"خیر","label1"=>"بله",
"value"=>$line_product_station->is_ability_to_choose_next_station??0])

@include("component.input._number",["id"=>"setup_time_for_final_setting",'label'=>"مدت زمان تنظمیات پایانی","value"=>$line_product_station->setup_time_for_final_setting??""])
@include("component.input._number",["id"=>"setup_time_for_co_channel",'label'=>"مدت زمان ستاب هم کانال(دقیقه)","value"=>$line_product_station->setup_time_for_co_channel??""])
@include("component.input._number",["id"=>"setup_time_for_non_co_channel",'label'=>"مدت زمان ستاپ غیر هم کانال(دقیقه)","value"=>$line_product_station->setup_time_for_non_co_channel??""])

@include("component.input._radio_box01",[
    "id"=>"has_control_sample","label"=>"آیا مسیر محصول نیاز به نمونه شاهد (بایگانی یک نمونه از کالا در هر بار تولید) دارد؟","label0"=>"خیر","label1"=>"بله",
"value"=>$line_product_station->has_control_sample??0])

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
    "id"=>"line_product_start_status_id",
    "label"=>" نوع پیش نیازی شروع عملیات ماشین با توجه به مسیر محصول قبلی ",
    "option"=>$line_product_start_status_option["items"],
    "val"=>$line_product_start_status_option["value"],
    "text"=>$line_product_start_status_option["text"],
    "class_col"=>""
    ])
</div>
@include("component.input._number",["id"=>"delay_in_the_start_minute",'label'=>"تاخیر در شروع عملیات های (STS) ( دقیقه)","value"=>$line_product_station->delay_in_the_start_minute??""])


<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
    "id"=>"production_method_id",
    "label"=>" توضیحات روش تولید ",
    "option"=>$production_methods_option["items"],
    "val"=>$production_methods_option["value"],
    "text"=>$production_methods_option["text"],
    "class_col"=>""
    ])
</div>

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
    "id"=>"status_id",
    "label"=>" وضعیت ",
    "option"=>$status_option["items"],
    "val"=>$status_option["value"],
    "text"=>$status_option["text"],
    "class_col"=>""
    ])
</div>