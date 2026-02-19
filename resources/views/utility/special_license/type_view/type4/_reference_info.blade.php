@include("component.input._lable",["lable"=>" درخواست خروج از انبار","value"=>$reference->code])
@include("component.input._lable",["lable"=>" کالا","value"=>$object1->product->fullCaption()])
<div class="col-md-4">
    @include("component.input._aotocomplet2",[
        "id"=>"packing_type_id",
        "label"=>"نوع بسته بندی ",
        "option"=>$packing_type_option["items"],
        "val"=>$packing_type_option["value"],
        "text"=>$packing_type_option["text"],
        "class_col"=>""
        ])
</div>
<div class="w-100"></div>
<div class="col-md-4">
    @include("component.input._checkbox_simple",[
        "id"=>"add_to_product_packing",
        "checked"=>1,
        "label"=>" بعد از تایید مجوز، بسته بندی به لیست بسته بندی های مجاز کالا اضافه گردد.",

        "val"=>1,
        "class_col"=>""
        ])
</div>
<br/>
<br/>


