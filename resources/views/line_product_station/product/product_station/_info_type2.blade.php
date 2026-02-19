<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"supplier_id",
        "label"=>" تامین کننده ",
        "option"=>$supplier_option["items"],
        "val"=>$supplier_option["value"],
        "text"=>$supplier_option["text"],
        "class_col"=>""
        ])
</div>

<div class="w-100"><br></div>

@include("component.input._number",["id"=>"receiving_time",'label'=>"زمان دریافت کالا (ساعت)","value"=>$line_product_station->receiving_time??""])


@include("component.input._number",["id"=>"purchasing_capacity",'label'=>("ظرفیت خرید (".$product->unit->caption." در یک ماه)"),"value"=>$line_product_station->purchasing_capacity??""])
@include("component.input._number",["id"=>"min_of_production",'label'=>"حداقل خرید از تامین کننده","value"=>$line_product_station->min_of_production??""])
@include("component.input._number",["id"=>"max_of_production",'label'=>"حداکثر خرید از تامین کننده  ","value"=>$line_product_station->max_of_production??""])
@include("component.input._number",["id"=>"batch",'label'=>"بچ خرید","value"=>$line_product_station->batch??""])
@include("component.input._number",["id"=>"extra_production",'label'=>"تعداد اضافه خرید","value"=>$line_product_station->extra_production??""])
@include("component.input._number",["id"=>"percent_of_extra_production",'label'=>"درصد اضافه خرید ","value"=>$line_product_station->percent_of_extra_production??""])
@include("component.input._text",["id"=>"product_caption_in_supplier_system",'label'=>"نام کالا در سامانه تامین کننده ","value"=>$line_product_station->product_caption_in_supplier_system??""])
@include("component.input._text",["id"=>"product_code_in_supplier_system",'label'=>"کد کالا در سامانه تامین کننده ","value"=>$line_product_station->product_code_in_supplier_system??""])

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
