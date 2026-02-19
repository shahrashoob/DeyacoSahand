<form id="form1" action="{{route("line_product_station.product.update_supplementary",$product)}}"
      method="post"
      autocomplete="off"
      novalidate="novalidate"
      enctype="multipart/form-data">
    @csrf

    <div class="row">

        <div class="w-100"><br/></div>
        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"goods_type_id",
                "label"=>" نوع کالا",
                "option"=>$goods_type_option["items"],
                "val"=>$product->goods_type->id??"",
                "text"=>$product->goods_type->caption??"",
                "class_col"=>""
                ])
        </div>
        <div class="w-100"><br/></div>
        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"unit_id",
                "label"=>" واحد کالا  ",
                "option"=>$unit_option["items"],
                "val"=>$product->unit->id??"",
                "text"=>$product->unit->caption??"",
                "class_col"=>""
                ])
        </div>
        <div class="w-100"><br/></div>
        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"sub_unit_id",
                "label"=>" واحد فرعی کالا  ",
                "option"=>$sub_unit_option["items"],
                "val"=>$product->sub_unit->id??"",
                "text"=>$product->sub_unit->caption??"",
                "class_col"=>""
                ])
        </div>
        <div class="w-100"><br/></div>
        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"sub_unit2_id",
                "label"=>" واحد فرعی 2 کالا  ",
                "option"=>$sub_unit2_option["items"],
                "val"=>$product->sub_unit2->id??"",
                "text"=>$product->sub_unit2->caption??"",
                "class_col"=>""
                ])
        </div>

        @include("component.input._number",["id"=>"frame_ratio_unit2",'label'=>"نسبت واحد فرعی 2 به واحد اصلی","value"=>$product->frame_ratio_unit2])


        <div class="w-100"><br/></div>
        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"unit_of_measure_type_id_in_production",
                "label"=>" ترتیب اهمیت واحد های کالا در ماشین  ",
                "option"=>$unit_of_measure_type_in_production_option["items"],
                "val"=>$unit_of_measure_type_in_production_option["value"],
                "text"=>$unit_of_measure_type_in_production_option["text"],
                "class_col"=>""
                ])
        </div>
        <div class="w-100"><br/></div>
        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"unit_of_measure_type_id_in_sale",
                "label"=>" ترتیب اهمیت واحد های کالا در فروش  ",
                "option"=>$unit_of_measure_type_in_sale_option["items"],
                "val"=>$unit_of_measure_type_in_sale_option["value"],
                "text"=>$unit_of_measure_type_in_sale_option["text"],
                "class_col"=>""
                ])
        </div>

        <div class="col-md-12" style="margin-top: 15px">
            <a href="{{route("line_product_station.product.index")}}" class="btn btn-outline-dark">بازگشت به داشبورد</a>

            <a href="{{route("line_product_station.product.edit",$product)}}" class="btn btn-outline-dark"> مرحله
                قبل </a>

            <button type="submit" class="btn btn-primary"> ذخیره و ادامه</button>


            <a href="{{route("line_product_station.product.sale.index",$product)}}" class="btn btn-outline-dark">مرحله
                بعد </a>

        </div>


    </div>


</form>
