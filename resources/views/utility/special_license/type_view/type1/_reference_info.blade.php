@php $product=$special_license_type->getObject1($param1);@endphp
@include("component.input._lable",["lable"=>"شماره درخواست خروج از انبار","value"=>$reference->code])
@include("component.input._lable",["label"=>"نام کالا","value"=>$product->caption])
@include("component.input._lable",["label"=>"مقدار کالا در درخواست","value"=>$param2." ".$product->unit->caption])
@if($param3 > 0)
    @include("component.input._lable",["label"=>"مقدار کالا برای مجوز ","value"=>$param3." ".$product->unit->caption])
@else
    @include("component.input._number",["id"=>"param3","label"=>"مقدار کالا برای مجوز ","value"=>$param3." ".$product->unit->caption,"class_col"=>"col-md-3"])
<div class="w-100"></div>
@endif


