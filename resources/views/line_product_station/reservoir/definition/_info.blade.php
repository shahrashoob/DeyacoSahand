@include("component.input._text",["id"=>"caption",'label'=>"نام مخزن","value"=>$reservoir->caption??""])

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"reservoir_type_id",
        "label"=>" نوع مخزن ",
        "option"=>$reservoir_type_option["items"],
        "val"=>$reservoir_type_option["value"],
        "text"=>$reservoir_type_option["text"],
        "class_col"=>""
        ])
</div>
<div class="w-100"><br/></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"unit_id",
        "label"=>" واحد اصلی ",
        "option"=>$unit_option["items"],
        "val"=>$unit_option["value"],
        "text"=>$unit_option["text"],
        "class_col"=>""
        ])
</div>
<div class="w-100"><br/></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"warehouse_id",
        "label"=>" انبار",
        "option"=>$warehouse_option["items"],
        "val"=>$warehouse_option["value"],
        "text"=>$warehouse_option["text"],
        "class_col"=>""
        ])
</div>
<div class="w-100"><br/></div>
@include("component.input._number",["id"=>"capacity","label"=>"ظرفیت","value"=>$reservoir->capacity??""])

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"active_status_id",
        "label"=>" وضعیت ",
        "option"=>$active_status_option["items"],
        "val"=>$active_status_option["value"],
        "text"=>$active_status_option["text"],
        "class_col"=>""
        ])
</div>
<div class="w-100"><br/></div>
