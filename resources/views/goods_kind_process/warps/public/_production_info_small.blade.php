<div class="row">


    @include("component.input._lable",["id"=>"","lable"=>"   تاریخ ابلاغ","value"=>$production->get_create_date()])

   @include("component.input._lable",["id"=>"","lable"=>"   ساعت ابلاغ ","value"=>$production->get_create_time()])


    @include("component.input._lable_product",["id"=>"product1".$production->id,"lable"=>"محصول",
             "product_property"=>$production->product,
             "value"=>$production->product->code."-".$production->product->caption])


    @include("component.input._lable",["id"=>"","lable"=>"   نوع کارت ","value"=>$production->production_type->caption??""])
    @include("component.input._lable",["id"=>"","lable"=>"   کانال تولید ","value"=>$production->product->production_channel->caption??""])

    @include("component.input._lable",["id"=>"","lable"=>"   مقدار ","value"=>$production->number()])
    @include("component.input._lable",["id"=>"","lable"=>"   مقدار تخصیص داده شده ","value"=>$production->get_allocation_amount()." ".($production->product->unit->caption??"")])

    @if($production->packing_type)
        @include("component.input._lable",["id"=>"","lable"=>"   نوع بسته بندی مجاز ","value"=>$production->packing_type->caption])
    @endif

    @include("component.input._lable",["id"=>"","lable"=>"   وضعیت ","value"=>$production->getStatus()])
    @include("component.input._lable",["id"=>"","lable"=>"   حداکثر تاریخ تحویل ","value"=>$production->get_max_delivery_date()])

    @include("component.input._lable",["id"=>"","lable"=>" شرح درخواست ","value"=>$production->order_list->description_request->text??""])


</div>
