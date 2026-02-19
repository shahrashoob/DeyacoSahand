<div class="row">
    @include("component.input._lable",["id"=>"code1",'label'=>"کد کالا","value"=>$product->code??""])
    @include("component.input._lable",["id"=>"caption1",'label'=>"نام کالا ","value"=>$product->caption??""])
    @include("component.input._lable",["id"=>"goods_kind_id1",'label'=>"رسته کالا ","value"=>$product->goods_kind->caption??""])
    @include("component.input._lable",["id"=>"number_in_carton1",'label'=>"تعداد در واحد اصلی ( ویژه انتقال به نوسا)","value"=>$product->number_in_carton??""])
    @include("component.input._lable",["id"=>"weight1",'label'=>" وزن کالا (کیلوگرم)","value"=>$product->weight??""])
    @if($product->predictive_weight)
    @include("component.input._lable",["id"=>"predictive_weight",'label'=>"وزن پیش بینی ","value"=>$product->predictive_weight." کیلوگرم"??""])
    @endif
    @include("component.input._lable",["id"=>"supply_type_id1",'label'=>" نوع تامین ","value"=>$product->supply_type->caption??""])
    @if($product->supply_type_id==3)
    @include("component.input._lable",["id"=>"service_id_in_employer_system",'label'=>" کد خدمت در سامانه کارفرما ","value"=>$product->service_in_employer_system->caption??""])
    @endif
    @include("component.input._lable",["id"=>"active_status_id1",'label'=>"  وضعیت  ","value"=>$product->active_status->caption??""])
    <br/>
    <div class="col-md-6">
        <img style="width: 300px" src="{{asset("upload/product/".($product->image->filename??''))}}"
             onerror="this.onerror=null;this.src='{{url("upload/product/product.png")}}';"
        />
    </div>
    <br/>
</div>

