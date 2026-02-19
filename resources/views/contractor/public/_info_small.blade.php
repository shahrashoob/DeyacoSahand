<div class="row">


    @include("component.input._lable",["id"=>"","lable"=>"   تاریخ ابلاغ","value"=>$production->get_create_date()])

    @include("component.input._lable",["id"=>"","lable"=>"   ساعت ابلاغ ","value"=>$production->get_create_time()])


    @include("component.input._lable",["id"=>"","lable"=>" محصول ","value"=>$production->product->fullCaption()])


    @include("component.input._lable",["id"=>"","lable"=>"   مقدار ","value"=>$production->number()])
    @include("component.input._lable",["id"=>"","lable"=>"   مقدار تخصیص داده شده ","value"=>$production->get_allocation_amount(false,3)])
    @include("component.input._lable",["id"=>"","lable"=>"کانال تولید","value"=>$production_channel_type->caption??""])

    @if($production->packing_type)
        @include("component.input._lable",["id"=>"","lable"=>"   نوع بسته بندی مجاز ","value"=>$production->packing_type->caption])
    @endif
    @include("component.input._lable",["id"=>"","lable"=>"   وضعیت ","value"=>$production->getStatus()])

    @include("component.input._lable",["id"=>"","lable"=>" شرح درخواست ","value"=>$production->order_list->description_request->text??""])


</div>
