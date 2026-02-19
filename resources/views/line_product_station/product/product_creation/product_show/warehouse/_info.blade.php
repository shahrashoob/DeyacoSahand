<div class="row">
    @include("component.input._lable",["id"=>"warehouse_storage_type_id",'label'=>"نوع انبارش   ","value"=>$product->warehouse_storage_type->caption])
                @include("component.input._lable",["id"=>"min_inventory",'label'=>" حداقل موجودی (نقطه سفارش)","value"=>$product->min_inventory])
                @include("component.input._lable",["id"=>"max_inventory",'label'=>"حداکثر موجودی  ","value"=>$product->max_inventory])
                @include("component.input._lable",["id"=>"packing_type",'label'=>"بسته بندی پیش فرض تولید  ","value"=>$product->default_packing_type->caption??"---"])


</div>
@include($view_path."_btn_list")