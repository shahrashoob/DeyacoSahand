<div class="row">

                       
    @include("component.input._lable",["id"=>"","lable"=>" سریال  تولید","value"=>$production->production_series])




    @include("component.input._lable",["id"=>"","lable"=>"   تاریخ ابلاغ","value"=>
    
    jdate( \Carbon\Carbon::parse($production->order_time)->timestamp)->format('%A, %d %B %y')])
   

        
   @include("component.input._lable",["id"=>"","lable"=>" محصول ","value"=>$production->product->code." - ".$production->product->caption])

    @include("component.input._lable",["id"=>"","lable"=>"   نام محصول ","value"=>$production->product_name])
    @include("component.input._lable",["id"=>"","lable"=>"   تعداد ","value"=>$production->number])


    @include("component.input._lable",["id"=>"","lable"=>"   واحد سنجش","value"=>$production->measurment_unit])


</div>  