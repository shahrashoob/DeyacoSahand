@php $production=$special_license_type->getObject1($param1);@endphp
{{--@php $day_number=$param2; @endphp--}}

@include("component.input._lable",["label"=>" بسته بندی ","value"=>$reference->code])
@if($production)
    @include("component.input._lable",["label"=>"کارت تولید","value"=>$production->serial()." - ".$production->product->caption])
@endif
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"replace_production_id",
        "label"=>"کارت پیمان جایگزین",
        "option"=>$option_17,
        "val"=>"",
        "text"=>"",
        "class_col"=>"col-md-6"
        ])
</div>

<div class="w-100"></div>