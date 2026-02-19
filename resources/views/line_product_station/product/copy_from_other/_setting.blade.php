<div class="row">

    @if($get_new_product_code)
    @include("component.input._text",["id"=>"code",'label'=>"کد کالای جدید","value"=>"","autofocus"=>1])
    @include("component.input._text",["id"=>"caption",'label'=>"نام کالای جدید ","value"=>""])
    @endif

    <div class="col-md-6">
        @include("component.input._aotocomplet2",[
            "id"=>"product_id",
            "label"=>" کپی اطلاعات از کالای ",
            "option"=>$product_option["items"],
            "val"=>"",
            "text"=>"",
            "class_col"=>""
            ])
    </div>

    @include("line_product_station.product.copy_from_other._setting_items")

</div>