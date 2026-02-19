<form id="form2" action="{{route("line_product_station.goods_kind.setting.init_info_store",$goods_kind)}}" method="post"
      autocomplete="off"
      novalidate="novalidate">
    @csrf
    @include("component.input._hidden",["id"=>"tab_index","value"=>"consumed"])
    <div class="w-100"></div>
    <div class="col-md-9" data-select2-id="119">

        @include("component.input.select2._select2",[
               "id"=>"default_consumed_goods_kinds",
               "label"=>" رسته های کالایی مجاز جهت کالای مصرفی   ",
               "option"=>$goods_kind_option["items"],
               "class_col"=>""
       ])
    </div>
    <div class="col-md-12">
        <br/>
        <a href="{{route("line_product_station.goods_kind.index")}}" class="btn btn-outline-dark">بازگشت</a>

        <button type="submit" class="btn btn-primary"> ذخیره</button>
    </div>
</form>
