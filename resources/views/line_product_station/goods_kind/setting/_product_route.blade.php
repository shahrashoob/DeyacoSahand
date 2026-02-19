<form id="form3" action="{{route("line_product_station.goods_kind.setting.init_info_store",$goods_kind)}}" method="post"
      autocomplete="off"
      novalidate="novalidate">
    @csrf
    @include("component.input._hidden",["id"=>"tab_index","value"=>"product_route"])

    <div class="w-100"></div>
    <div class="col-md-9" data-select2-id="119">

        @include("component.input.select2._select2",[
               "id"=>"default_line_ids",
               "label"=>" خط های تولید مجاز   ",
               "option"=>$line_option["items"],
               "class_col"=>""
       ])
    </div>
    <div class="col-md-12">
        <br/>
        <a href="{{route("line_product_station.goods_kind.index")}}" class="btn btn-outline-dark">بازگشت</a>

        <button type="submit" class="btn btn-primary"> ذخیره</button>
    </div>
</form>
