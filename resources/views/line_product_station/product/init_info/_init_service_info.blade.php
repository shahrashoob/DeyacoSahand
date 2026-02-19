<div class="row">
    @include("component.input._text",["id"=>"code2",'label'=>"کد خدمت","value"=>$product->code,"autofocus"=>1])
    @include("component.input._text",["id"=>"caption2",'label'=>"نام خدمت ","value"=>$product->caption])


    @include("line_product_station.product.product_creation.basic_information_service._info_service")
    <div class="w-100"><br/></div>

    <div class="col-md-6">
        @include("component.input._select",[
            "id"=>"service_id_in_employer_system2",
            "label"=>"  کد خدمت در سامانه کارفرما ",
            "option"=>$service_id_in_employer_system_option["items"],
            "val"=>$service_id_in_employer_system_option["value"],
            "text"=>$service_id_in_employer_system_option["text"],
            "class_col"=>""
            ])
    </div>
    <div class="w-100"><br/></div>

    <div class="col-md-6">
        @include("component.input._select",[
            "id"=>"active_status_id2",
            "label"=>" وضعیت   ",
            "option"=>$status_option["items"],
            "val"=>$product->active_status->id??"",
            "text"=>$product->active_status->caption??"",
            "class_col"=>""
            ])
    </div>


</div>

