<div class="row">

    <div class="col-md-6">
        @include("component.input.select2._select2",[
            "id"=>"tariff_ids",
            "label"=>" نوع تعرفه ",
            "option"=>$tariff_option["items"],
            "val"=>$tariff_option["value"],
            "text"=>$tariff_option["text"],
            "class_col"=>""
            ])
    </div>
    <div class="w-100"></div>
    <div class="col-md-6">
        @include("component.input.select2._select2",[
            "id"=>"packing_type_ids",
            "label"=>" نوع بسته بندی ",
            "option"=>$packing_type_option["items"],
            "val"=>$packing_type_option["value"],
            "text"=>$packing_type_option["text"],
            "class_col"=>"",
            "link"=>["url"=>route($route_path."change_packing_type",[$product,$product_creation_process->id??null]),"icon"=>"plus","caption"=>"افزودن نوع بسته بندی جدید"]
            ])

    </div>
    <div class="col-md-6">
        <br/>

    </div>
    <div class="w-100"></div>
    <div class="col-md-6">
        @include("component.input.select2._select2",[
            "id"=>"degree_ids",
            "label"=>" درجه ",
            "option"=>$degree_option["items"],
            "val"=>$degree_option["value"],
            "text"=>$degree_option["text"],
            "class_col"=>""
            ])
    </div>
    <div class="w-100"></div>
    <div class="col-md-6">
        @include("component.input._select",[
            "id"=>"type_of_sale_of_product_id",
            "label"=>" نوع فروش ",
            "option"=>$type_of_sale_product_option["items"],
            "val"=>$type_of_sale_product_option["value"],
            "text"=>$type_of_sale_product_option["text"],
            "class_col"=>""
            ])
    </div>
    <div class="w-100"><br/></div>
    <div class="col-md-6">
        @include("component.input._select",[
            "id"=>"warehouse_id",
            "label"=>"انبار",
            "option"=>$warehouse_option["items"],
            "val"=>$warehouse_option["value"],
            "text"=>$warehouse_option["text"],
            "class_col"=>""
            ])
    </div>
    <div class="w-100"><br/></div>

    @include("component.input._number",["id"=>"min_buy",'label'=>"حداقل خرید ","value"=>$product_tariff->min_buy??""])
    @include("component.input._number",["id"=>"max_buy",'label'=>"حداکثر خرید ","value"=>$product_tariff->max_buy??""])


    @include("component.input._hidden",["id"=>"increase_percentage_deadline_per_day",'label'=>"به قیمت واحد فاکتور با  توجه به راس پرداخت،   n درصد به ازای  هر روز اضافه شود","value"=>$tariff_row_item->increase_percentage_deadline_per_day??"0"])
    @include("component.input._number",["id"=>"tax",'label'=>"درصد مالیات","value"=>$product_tariff->tax??""])
    @include("component.input._number",["id"=>"fare",'label'=>"درصد ارزش افزوده","value"=>$product_tariff->fare??""])

    @include("component.input._text",["id"=>"customer_product_code",'label'=>"کد کالای مشتری","value"=>$product_tariff->customer_product_code??""])
    @include("component.input._text",["id"=>"customer_product_caption",'label'=>"نام کالای مشتری","value"=>$product_tariff->customer_product_caption??""])

</div>