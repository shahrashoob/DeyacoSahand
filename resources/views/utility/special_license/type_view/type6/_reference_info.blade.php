@include("component.input._lable",["lable"=>"  بسته بندی","value"=>$reference->code])
@include("component.input._lable",["lable"=>"وزن خالص محاسبه شده توسط سامانه","value"=>round($param1,3)])
@include("component.input._lable",["lable"=>"وزن خالص واقعی","value"=>round($param2,3)])

