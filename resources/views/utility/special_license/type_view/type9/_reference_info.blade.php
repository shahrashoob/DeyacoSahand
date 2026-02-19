@include("component.input._lable",["label"=>"برای فرم ورود به انبار ","value"=>$reference->code])

@if(isset($reference->allocation->supplier_id))
    @include("component.input._lable",["label"=>"تامین کننده","value"=>$reference->allocation->supplier->caption])
    @include("component.input._hidden",["id"=>"supplier_id","value"=>$reference->allocation->supplier->id])
@else
    @include("component.input._select",[
                               "id"=>"supplier_id",
                               "label"=>"تامین کننده",
                               "option"=>$supplier_option["items"],
                               "val"=>$supplier_option["value"],
                               "text"=>$supplier_option["text"],
                               "class_col"=>"col-md-3",
                               ])
    <div class="w-100"><br/></div>
@endif




