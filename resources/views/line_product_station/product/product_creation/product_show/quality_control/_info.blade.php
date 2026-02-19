<div class="row">
    @include("component.input._radio_box01",["id"=>"have_testing_before_production","label"=>"آیا کالا قبل از تولید تست دارد؟","value"=>$product->have_testing_before_production])
    @include("component.input._radio_box01",["id"=>"testing_is_on_line_production","label"=>"آیا تست بر روی خط تولید انجام می شود؟","value"=>$product->testing_is_on_line_production])


    @include("component.input._lable",["id"=>"testing_amount",'label'=>"مقدار تست کالا ","value"=>$product->testing_amount." ".$product->unit->caption])


</div>
@include($view_path."_btn_list")