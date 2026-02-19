@include("component.input._lable",["id"=>"caption",'label'=>"درخواست دهنده ","value"=>$product_creation_process->worker->fullName()])
@include("component.input._lable",["id"=>"caption",'label'=>"نام پیشنهادی  ","value"=>$product_creation_process->caption])

@if($product_creation_process->product)
    @include("component.input._lable",["id"=>"caption",'label'=>$product_creation_process->product_service_type_id==1?"نام کالا":"نام خدمت","value"=>$product_creation_process->product->caption,"url"=>$url_product??null])

    @include("component.input._lable",["id"=>"caption",'label'=>$product_creation_process->product_service_type_id==1?"کد کالا":"کد خدمت","value"=>$product_creation_process->product->code??""])
@endif

@include("component.input._lable",["id"=>"caption",'label'=>"نوع (کالا/خدمت)  ","value"=>$product_creation_process->product_service_type->caption])

@if($product_creation_process->goods_kind)
    @include("component.input._lable",["id"=>"goods_kind_id",'label'=>"رسته (کالا/خدمت) ","value"=>$product_creation_process->goods_kind->caption??"***"])
@endif

@if($product_creation_process->product)
    @include("component.input._lable",["id"=>"predictive_weight",'label'=>"وزن پیش بینی ","value"=>$product_creation_process->product->predictive_weight." کیلوگرم"])
@endif

@include("component.input._lable",["id"=>"predictive_weight",'label'=>"آیا نیاز به تایید نمونه کالا می باشد؟","value"=>$product_creation_process->has_sampling_required?"بله":"خیر"])


@if($product_creation_process->sample_production)
    @include("component.input._lable",["id"=>"sample_production",'label'=>"کارت تولید نمونه گیری ","value"=>$product_creation_process->sample_production->serial,"message"=>$product_creation_process->sample_production->getStatus()])
@endif
@include("component.input._lable",["id"=>"caption",'label'=>"وضعیت","value"=>$product_creation_process->status->caption??""])
