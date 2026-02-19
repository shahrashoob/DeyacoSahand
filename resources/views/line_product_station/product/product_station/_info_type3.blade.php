<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"contractor_id",
        "label"=>" پیمانکار ",
        "option"=>$contractor_option["items"],
        "val"=>$contractor_option["value"],
        "text"=>$contractor_option["text"],
        "class_col"=>""
        ])
</div>


<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"contractor_operation_id",
        "label"=>" عملیات پیمانکار ",
        "option"=>$contractor_operation_option["items"],
        "val"=>$contractor_operation_option["value"],
        "text"=>$contractor_operation_option["text"],
        "class_col"=>""
        ])
</div>



<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
    "id"=>"production_channel_type_id",
    "label"=>" نوع کانال پیمان ",
    "option"=>$contractor_channel_type_option["items"],
    "val"=>$contractor_channel_type_option["value"],
    "text"=>$contractor_channel_type_option["text"],
    "class_col"=>""
    ])
</div>
@include("component.input._number",["id"=>"efficiency",'label'=>"کارایی شاخص خرد","value"=>$line_product_station->efficiency??""])
@include("component.input._number",["id"=>"delivery_time",'label'=>"زمان تحویل کالا (ساعت)","value"=>$line_product_station->delivery_time??""])
@include("component.input._number",["id"=>"receiving_time",'label'=>"زمان دریافت کالا (ساعت)","value"=>$line_product_station->receiving_time??""])

@include("component.input._number",["id"=>"setup_time",'label'=>"زمان انتظار شروع به کار(دقیقه)","value"=>$line_product_station->setup_time??""])

@include("component.input._number",["id"=>"practical_capacity_of_production",'label'=>("ظرفیت عملی تولید (".$product->unit->caption." در ساعت)"),"value"=>$line_product_station->practical_capacity_of_production??""])
@include("component.input._number",["id"=>"min_of_production",'label'=>"حداقل تولید پیمانکار","value"=>$line_product_station->min_of_production??""])
@include("component.input._number",["id"=>"max_of_production",'label'=>"حداکثر تولید پیمانکار  ","value"=>$line_product_station->max_of_production??""])
@include("component.input._number",["id"=>"batch",'label'=>"بچ تولید","value"=>$line_product_station->batch??""])
@include("component.input._number",["id"=>"extra_production",'label'=>"تعداد اضافه تولید پیمانکار","value"=>$line_product_station->extra_production??""])
@include("component.input._number",["id"=>"percent_of_extra_production",'label'=>"درصد اضافه تولید پیمانکار ","value"=>$line_product_station->percent_of_extra_production??""])

@include("component.input._text",["id"=>"product_code_in_contractor_system",'label'=>"کد کالا در سامانه پیمانکار ","value"=>$line_product_station->product_code_in_contractor_system??""])
@include("component.input._text",["id"=>"service_code_in_contractor_system",'label'=>"کد خدمت در سامانه پیمانکار ","value"=>$line_product_station->service_code_in_contractor_system??""])

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
