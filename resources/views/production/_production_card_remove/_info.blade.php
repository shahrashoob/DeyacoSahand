<div class="row">


    @include("component.input._lable",["id"=>"","lable"=>"   تاریخ ابلاغ","value"=>$production->get_create_date()])
   
   @include("component.input._lable",["id"=>"","lable"=>"   ساعت ابلاغ ","value"=>$production->get_create_time()])

        
   @include("component.input._lable",["id"=>"","lable"=>" محصول ","value"=>$production->product->code." - ".$production->product->caption])


    @include("component.input._lable",["id"=>"","lable"=>"   تعداد ","value"=>$production->number()])

    @include("component.input._lable",["id"=>"","lable"=>"   کد خط ","value"=>$production->line->caption??""])

    @include("component.input._lable",["id"=>"","lable"=>" ساعت شروع","value"=>""])

    @include("component.input._lable",["id"=>"","lable"=>" ساعت پایان ","value"=>""])

    @include("component.input._lable",["id"=>"","lable"=>" در صد ضایعات  ","value"=>""])


    @include("component.input._lable",["id"=>"","lable"=>"  قیمت مصرف کننده","value"=>""])

    @include("component.input._lable",["id"=>"","lable"=>"تاریخ انقضا","value"=>""])

    
    @include("component.input._lable",["id"=>"","lable"=>"توضیحات ","value"=>$production->order->description_request->text??"" ])

</div>  