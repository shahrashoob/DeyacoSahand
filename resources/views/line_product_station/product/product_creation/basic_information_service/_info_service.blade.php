<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"exist_product_service_type_id",
        "label"=>" نوع خروجی خدمت ",
        "option"=>$exist_product_service_type_option["items"],
        "val"=>$product->exist_product_service_type->id??"",
        "text"=>$product->exist_product_service_type->caption??"",
        "class_col"=>""
        ])
</div>
<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"unit_id2",
        "label"=>" واحد  خروجی خدمت ",
        "option"=>$unit_option["items"],
        "val"=>$product->sub_unit->id??"",
        "text"=>$product->sub_unit->caption??"",
        "class_col"=>""
        ])
</div>
<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"sub_unit_id2",
        "label"=>" واحد فرعی خروجی خدمت ",
        "option"=>$sub_unit_option["items"],
        "val"=>$product->sub_unit->id??"",
        "text"=>$product->sub_unit->caption??"",
        "class_col"=>""
        ])
</div>
<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"sub_unit2_id2",
        "label"=>" واحد فرعی 2 خروجی خدمت  ",
        "option"=>$sub_unit2_option["items"],
        "val"=>$product->sub_unit2->id??"",
        "text"=>$product->sub_unit2->caption??"",
        "class_col"=>""
        ])
</div>


@include("component.input._number",["id"=>"number_in_carton2",'label'=>"تعداد در واحد اصلی ( ویژه انتقال به نوسا)","value"=>$product->number_in_carton??""])
<div class="w-100"></div>

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._select",[
        "id"=>"supply_type_id2",
        "label"=>" نوع تامین   ",
        "option"=>$supply_type_option["items"],
        "val"=>$product->supply_type->id??"",
        "text"=>$product->supply_type->caption??"",
        "class_col"=>""
        ])
</div>
