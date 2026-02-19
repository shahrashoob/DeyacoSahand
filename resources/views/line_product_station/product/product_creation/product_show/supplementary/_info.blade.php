<div class="row">
    @if($product->product_service_type_id == 1)
        @include("component.input._lable",["id"=>"goods_type_id",'label'=>" نوع کالا","value"=>$product->goods_type->caption??""])
    @endif
    @include("component.input._lable",["id"=>"unit_id",'label'=>"واحد کالا","value"=>$product->unit->caption??""])

    @include("component.input._lable",["id"=>"sub_unit_id",'label'=>"واحد فرعی کالا ","value"=>$product->sub_unit->caption??""])
    @if($product->sub_unit2)
        @include("component.input._lable",["id"=>"sub_unit2_id",'label'=>"واحد فرعی 2 کالا","value"=>$product->sub_unit2->caption??""])
    @endif

    @if($product->sub_unit2)
        @include("component.input._lable",["id"=>"frame_ratio_unit2",'label'=>"نسبت واحد فرعی ۲ به واحد اصلی ","value"=>$product->frame_ratio_unit2??""])
    @endif



        @include("component.input._lable",["id"=>"sub_unit_id",'label'=>"ترتیب اهمیت واحد های کالا در ماشین ","value"=>$product->unit_of_measure_type_in_production->caption??""])



        @include("component.input._lable",["id"=>"sub_unit_id",'label'=>"ترتیب اهمیت واحد های کالا در فروش ","value"=>$product->unit_of_measure_type_in_production->caption??""])
</div>

@include($view_path."_btn_list")
