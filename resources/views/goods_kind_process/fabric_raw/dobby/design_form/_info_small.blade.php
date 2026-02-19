<div class="row">


    @include("component.input._lable",["id"=>"","lable"=>" ماشین","value"=>$design_form->machine->code." - ".$design_form->machine->caption])


    @foreach($design_form->production_list as $item)
        @include("component.input._lable",["id"=>"","lable"=>"کارت تولید باند ".$item->band_code,"value"=>$item->production->serial])

        @include("component.input._lable_product",["id"=>"product".$item->id,"lable"=>"   پارچه خام باند ".$item->band_code,
    "product_property"=>$item->production->product,
    "value"=>$item->production->product->code."-".$item->production->product->caption])

    @endforeach
    {{--    @include("component.input._lable",["id"=>"","lable"=>" آیا طراحی در انبار وجود دارد؟","value"=>$design_form->getAnswer("design_available")])--}}



    {{--    @include("component.input._lable",["id"=>"","lable"=>" آیا طراحی لامل ریزی دارد؟","value"=>$design_form->getAnswer("it_has_pinning")])--}}



    {{--    @include("component.input._lable",["id"=>"","lable"=>" آیا چله در انبار هست؟","value"=>$design_form->getAnswer("warps_is_in_warehouse")])--}}



    {{--    @include("component.input._lable",["id"=>"","lable"=>" آیا طراحی نیاز به تبدیل دارد؟","value"=>$design_form->getAnswer("need_to_convert")])--}}



    @include("component.input._lable",["id"=>"","lable"=>" وضعیت","value"=>$design_form->status->caption])
</div>
