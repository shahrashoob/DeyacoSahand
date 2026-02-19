<div class="row">
    <div class="col-sm-12">
        <form id="form1" action="{{route($route_path."submit",[$product,$product_creation_process])}}" method="post"
              autocomplete="off"
              novalidate="novalidate">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    @include("component.input._select",[
                        "id"=>"product_planing_algorithm_id",
                        "label"=>" نوع الگوریتم برنامه ریزی  (تامین/تولید / پیمان) ",
                        "option"=>$product_planing_algorithm["items"],
                        "val"=>$product->product_planing_algorithm_id??"",
                        "text"=>$product->product_planing_algorithm->caption??"",
                        "class_col"=>""
                        ])
                </div>
                <div class="w-100"><br/></div>
                <div class="col-md-6">
                    @include("component.input._select",[
                        "id"=>"production_algorithm_type_id",
                        "label"=>"الگوریتم صدور کارت تولید",
                        "option"=>$production_algorithm["items"],
                        "val"=>$product->production_algorithm_type_id??"",
                        "text"=>$product->production_algorithm_type->caption??"",
                        "class_col"=>""
                        ])
                </div>
                <div class="w-100"><br/></div>
                <div class="col-md-6">
                    @include("component.input._select",[
                        "id"=>"order_point_algorithm_type_id",
                        "label"=>"الگوریتم نقطه سفارش (واحد اصلی کالا)",
                        "option"=>$order_point_algorithm["items"],
                        "val"=>$product->order_point_algorithm_type_id??"",
                        "text"=>$product->order_point_algorithm_type->caption??"",
                        "class_col"=>""
                        ])
                </div>
                <div class="w-100"><br/></div>
                <div class="col-md-6">
                    @include("component.input._select",[
                        "id"=>"lidetime_algorithm_type_id",
                        "label"=>"الگوریتم مدت زمان دریافت کالا(روز)",
                        "option"=>$lidetime_algorithm["items"],
                        "val"=>$product->lidetime_algorithm_type_id??"",
                        "text"=>$product->lidetime_algorithm_type->caption??"",
                        "class_col"=>""
                        ])
                </div>
            </div>

            <div class="w-100"><br/></div>
            <div class="row">
                <div class="col-md-12">
                    <h6>محل صدور کارت تولید :</h6>
                </div>
            </div>
                <div class="col-md-3">
                    @include("component.input._checkbox_simple",[
                        "id"=>"in_implementation",
                        "label"=>"دستور تولید ویژه دوره پیاده سازی",
                        "checked"=>$product->in_implementation == 1
                        ])
                        </div>
                        <div class="col-md-3">
                        @include("component.input._checkbox_simple",[
                        "id"=>"in_order_by_user",
                        "label"=>"در سفارش توسط اپراتور",
                        "checked"=>$product->in_order_by_user == 1
                        ])
                        </div>
                        <div class="col-md-3">
                        @include("component.input._checkbox_simple",[
                        "id"=>"in_order_by_diaco_script",
                        "label"=>"در سفارش توسط کارشناس دیجیتال دیاکو",
                        "checked"=>$product->in_order_by_diaco_script == 1
                        ])
                        </div>
                        <div class="col-md-3">
                        @include("component.input._checkbox_simple",[
                        "id"=>"by_deyaco_script",
                        "label"=>"کارشناس دیجیتال دیاکو",
                        "checked"=>$product->by_deyaco_script == 1
                        ])
                        </div>
            <br/>
            <br/>
            @include($view_path."_btn_list")
        </form>
    </div>

</div>
<script>

</script>
