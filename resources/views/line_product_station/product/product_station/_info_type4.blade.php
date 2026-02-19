<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"customer_id",
        "label"=>" مشتری ",
        "option"=>$customer_option["items"],
        "val"=>$customer_option["value"],
        "text"=>$customer_option["text"],
        "class_col"=>""
        ])
</div>
<div class="w-100"><br/></div>

@include("component.input._number",["id"=>"delivery_time",'label'=>"زمان تحویل کالا (ساعت)","value"=>$line_product_station->delivery_time??""])
@include("component.input._number",["id"=>"receiving_time",'label'=>"زمان دریافت کالا (ساعت)","value"=>$line_product_station->receiving_time??""])

@include("component.input._text",["id"=>"product_code_in_contractor_system",'label'=>"کد کالا در سامانه مشتری ","value"=>$line_product_station->product_code_in_contractor_system??""])
{{--@include("component.input._text",["id"=>"service_code_in_contractor_system",'label'=>"کد خدمت در سامانه مشتری ","value"=>$line_product_station->service_code_in_contractor_system??""])--}}

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"applicant_warehouse_id",
        "label"=>" انبار تحویل کالا ",
        "option"=>$applicant_warehouse_option["items"],
        "val"=>$applicant_warehouse_option["value"],
        "text"=>$applicant_warehouse_option["text"],
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
