<div class="row">

        @include("component.input._lable",["id"=>"product_service_type_id",'label'=>"نوع (کالا/خدمت)","value"=>$product->product_service_type->caption??""])

</div>

@if($product->product_service_type_id==1)
    @include("line_product_station.product.product_creation.product_show._init_product_info")
@else
    @include("line_product_station.product.product_creation.product_show._init_service_info")

@endif
<br/>
<a href="{{route("line_product_station.product.product_creation.dashboard.view",$product_creation_process)}}" class="btn btn-outline-dark">بازگشت به
    فرم طراحی</a>
<a href="{{route("line_product_station.product.product_creation.product_show.supplementary.index",$product_creation_process)}}"
   class="btn btn-outline-dark">مرحله بعد</a>