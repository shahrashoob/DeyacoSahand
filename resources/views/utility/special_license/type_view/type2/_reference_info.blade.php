
@include("component.input._lable",["lable"=>"شماره تخصیص","value"=>$reference->id])
@foreach($reference->items as $allocation_item)
    @include("component.input._lable",["label"=>"مقدار تخصیص ".$allocation_item->production->serial,"value"=>$allocation_item->allocation_amount." ".$allocation_item->product->unit->caption])
@endforeach

@include("component.input._lable",["label"=>"مقدار تولید شده ","value"=>$param1." ".$allocation_item->product->unit->caption])


