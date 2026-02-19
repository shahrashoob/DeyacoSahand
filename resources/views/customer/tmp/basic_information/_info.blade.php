@include("component.input._lable",["id"=>"caption",'label'=>__("input.applicant caption"),"value"=>$product_creation_process->worker->fullName(),"class_col"=>"col-md-6"])
@include("component.input._lable",["id"=>"caption",'label'=>__("input.suggested product name"),"value"=>$product_creation_process->caption,"class_col"=>"col-md-6"])

@include("component.input._lable",["id"=>"caption",'label'=>__("input.product_service_type_id"),"value"=>$product_creation_process->product_service_type->caption,"class_col"=>"col-md-6"])
@if($product_creation_process->goods_kind)
    @include("component.input._lable",["id"=>"goods_kind_id",'label'=>__("input.goods_kind_id"),"value"=>$product_creation_process->goods_kind->caption??"","class_col"=>"col-md-6"])
@endif
@include("component.input._lable",["id"=>"caption",'label'=>__("input.status_id"),"value"=>$product_creation_process->status->caption??"","class_col"=>"col-md-6"])

