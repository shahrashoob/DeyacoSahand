<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"owner_personal_id_in_ic",
        "label"=>"مالک ",
        "option"=>$owner_personal_option["items"],
        "val"=>$owner_personal_option["value"],
        "text"=>$owner_personal_option["text"],
        "class_col"=>""
        ])
</div>
@include("line_product_station.packing.packing_type._info")
<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"carrier_type_id",
        "label"=>" نوع حامل (اولین لایه)   ",
        "option"=>$carrier_option["items"],
        "class_col"=>""
        ])
</div>
<div class="w-100"></div>
<div class="col-md-6">
@include("component.input._aotocomplet2",[
    "id"=>"count_packing_layer",
    "label"=>" تعداد لایه بسته بندی",
    "option"=>$packing_layer_options,
    "class_col"=>""
    ])
</div>


@include("component.input._textarea",["id"=>"note",'label'=>"دستور العمل برگشت مواد اولیه","value"=>$packing_type->note??"","class_col"=>"col-md-9","height"=>"70px"])


