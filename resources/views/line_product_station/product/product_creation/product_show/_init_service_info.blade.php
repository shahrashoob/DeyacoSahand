<div class="row">
    @include("component.input._lable",["id"=>"code2",'label'=>"کد خدمت","value"=>$product->code??"","autofocus"=>1])
    @include("component.input._lable",["id"=>"caption2",'label'=>"نام خدمت ","value"=>$product->caption??""])

    @include("component.input._lable",["id"=>"product_service_type_id2",'label'=>"نوع خروجی خدمت ","value"=>$product->product_service_type->caption??""])
    @include("component.input._lable",["id"=>"unit_id2",'label'=>"واحد  خروجی خدمت","value"=>$product->sub_unit->caption??""])
    @include("component.input._lable",["id"=>"sub_unit_id2",'label'=>"واحد فرعی خروجی خدمت ","value"=>$product->sub_unit->caption??""])
    @include("component.input._lable",["id"=>"sub_unit2_id2",'label'=>"واحد فرعی 2 خروجی خدمت  ","value"=>$product->sub_unit2->caption??""])
    @include("component.input._lable",["id"=>"caption2",'label'=>"نام خدمت ","value"=>$product->caption??""])

    @include("component.input._lable",["id"=>"number_in_carton2",'label'=>"تعداد در واحد اصلی ( ویژه انتقال به نوسا)","value"=>$product->number_in_carton??""])
    @include("component.input._lable",["id"=>"supply_type_id2",'label'=>" نوع تامین","value"=>$product->supply_type->caption??""])


    @include("component.input._lable",["id"=>"service_id_in_employer_system2",'label'=>"کد خدمت در سامانه کارفرما ","value"=>$product->service_id_in_employer_system??""])
    @include("component.input._lable",["id"=>"active_status_id2",'label'=>"وضعیت ","value"=>$product->active_status->caption??""])



</div>

