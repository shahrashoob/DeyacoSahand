<div class="row">
    @include("component.input._lable",["id"=>"testing_amount",'label'=>"نوع الگوریتم برنامه ریزی ","value"=>$product->product_planing_algorithm->caption??""])
    @include("component.input._lable",["id"=>"testing_amount",'label'=>" الگوریتم صدور کارت تولید","value"=>$product->production_algorithm_type->caption??""])
    @include("component.input._lable",["id"=>"testing_amount",'label'=>"الگوریتم نقطه سفارش","value"=>$product->order_point_algorithm_type->caption??""])
    @include("component.input._lable",["id"=>"testing_amount",'label'=>"الگوریتم LidTime","value"=>$product->lidetime_algorithm_type->caption??""])
            <div class="row">
                <div class="col-md-12">
                    <h6>محل صدور کارت تولید :</h6>
                </div>
                    <div class="col-md-12">
                    @include("component.input._checkbox_simple",["id"=>"in_implementation", "label"=>"دستور تولید ویژه دوره پیاده سازی","checked"=>$product->in_implementation == 1 , "disabled"=>true])
                    </div>
                    <div class="col-md-12">
                    @include("component.input._checkbox_simple",["id"=>"in_order_by_user","label"=>"در سفارش توسط اپراتور","checked"=>$product->in_order_by_user == 1 , "disabled"=>true])
                    </div>
                    <div class="col-md-12">
                    @include("component.input._checkbox_simple",["id"=>"in_order_by_diaco_script","label"=>"در سفارش توسط کارشناس دیجیتال دیاکو","checked"=>$product->in_order_by_diaco_script == 1 , "disabled"=> true])
                    </div>
                    <div class="col-md-12">
                    @include("component.input._checkbox_simple",["id"=>"by_deyaco_script" ,"label"=>"کارشناس دیجیتال دیاکو","checked"=>$product->by_deyaco_script == 1 ,"disabled"=> true]) 
                    </div>
            </div> 
</div>
@include($view_path."_btn_list")