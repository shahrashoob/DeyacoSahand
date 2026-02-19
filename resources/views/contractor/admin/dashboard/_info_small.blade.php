<div class="row">


    @include("component.input._lable",["id"=>"","lable"=>"   تاریخ ابلاغ","value"=>$production->get_create_date()])

    @include("component.input._lable",["id"=>"","lable"=>"   ساعت ابلاغ ","value"=>$production->get_create_time()])

    @include("component.input._lable",["id"=>"","lable"=>"  حداکثر تاریخ تحویل ","value"=>$production->get_max_delivery_date()])


    @include("component.input._lable",["id"=>"","lable"=>" محصول ","value"=>$production->product->fullCaption()])


    @include("component.input._lable",["id"=>"","lable"=>"   مقدار ","value"=>$production->number()])
    @include("component.input._lable",["id"=>"","lable"=>"   مقدار تخصیص داده شده ","value"=>$production->get_allocation_amount(false,3)." ".$production->product->unit->caption])

    @if($production->packing_types)
        @php $message_packing_type="";
        foreach($production->packing_types as $item) $message_packing_type.=$item->packing_type->caption.", ";
        @endphp
        @include("component.input._lable",["id"=>"","lable"=>"   نوع بسته بندی مجاز ","value"=>$message_packing_type])
    @endif
    @include("component.input._lable",["id"=>"","lable"=>"   وضعیت ","value"=>$production->getStatus()])

    @include("component.input._lable",["id"=>"","lable"=>" شرح درخواست ","value"=>$production->order_list->description_request->text??""])


</div>
