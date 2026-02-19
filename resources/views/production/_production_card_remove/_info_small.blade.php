<div class="row">


    @include("component.input._lable",["id"=>"","lable"=>"   تاریخ ابلاغ","value"=>$production->get_create_date()])

   @include("component.input._lable",["id"=>"","lable"=>"   ساعت ابلاغ ","value"=>$production->get_create_time()])


   @include("component.input._lable",["id"=>"","lable"=>" محصول ","value"=>$production->product->fullCaption()])


    @include("component.input._lable",["id"=>"","lable"=>"   تعداد ","value"=>$production->number()])


    @include("component.input._lable",["id"=>"","lable"=>"   وضعیت ","value"=>$production->getStatus()])

    @include("component.input._lable",["id"=>"","lable"=>" شرح درخواست ","value"=>$production->order_list->description_request->text??""])


</div>
