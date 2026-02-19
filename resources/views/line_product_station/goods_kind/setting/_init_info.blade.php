<form id="form1" action="{{route("line_product_station.goods_kind.setting.init_info_store",$goods_kind)}}" method="post"
      autocomplete="off"
      novalidate="novalidate">
    @csrf
    <div class="w-100"></div>
    <div class="col-md-4" data-select2-id="119">

        @include("component.input.select2._select2",[
       "id"=>"default_unit_ids",
       "label"=>" واحد های اصلی مجاز   ",
       "option"=>$unit_option["items"],
       "class_col"=>""
       ])
    </div>

    @include("component.input._hidden",["id"=>"tab_index","value"=>"edit"])

    <div class="col-md-4" data-select2-id="119">

        @include("component.input.select2._select2",[
       "id"=>"default_sub_unit_ids",
       "label"=>" واحد های فرعی مجاز   ",
       "option"=>$sub_unit_option["items"],
       "class_col"=>""
       ])
    </div>

    <div class="col-md-4" data-select2-id="119">

        @include("component.input.select2._select2",[
           "id"=>"default_sub_unit2_ids",
           "label"=>" واحد های فرعی 2 مجاز   ",
           "option"=>$sub_unit2_option["items"],
           "class_col"=>""
       ])
    </div>

    <div class="col-md-4" data-select2-id="119">

        @include("component.input.select2._select2",[
           "id"=>"default_goods_type_ids",
           "label"=>" نوع های مجاز کالا   ",
           "option"=>$goods_type_option["items"],
           "class_col"=>""
          ])
    </div>

    <div class="w-100"><br/></div>
    <div class="col-md-4">
        @include("component.input.select2._select2",[
          "id"=>"default_unit_of_measure_type_ids_in_production",
          "label"=>" ترتیب اهمیت واحد های کالا در ماشین  ",
          "option"=>$unit_of_measure_type_in_production_option["items"],
          "class_col"=>""
          ])
    </div>
    <div class="w-100"><br/></div>
    <div class="col-md-4">
        @include("component.input.select2._select2",[
          "id"=>"default_unit_of_measure_type_ids_in_sale",
          "label"=>" ترتیب اهمیت واحد های کالا در فروش  ",
          "option"=>$unit_of_measure_type_in_sale_option["items"],
          "val"=>$unit_of_measure_type_in_sale_option["value"],
          "text"=>$unit_of_measure_type_in_sale_option["text"],
          "class_col"=>""
          ])
    </div>

    <div class="w-100"><br/></div>
    <div class="col-md-12">
        <br/>
        <a href="{{route("line_product_station.goods_kind.index")}}" class="btn btn-outline-dark">بازگشت</a>

        <button type="submit" class="btn btn-primary"> ذخیره  </button>
    </div>

</form>
