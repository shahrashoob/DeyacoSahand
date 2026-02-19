<form id="form4" action="{{route("line_product_station.goods_kind.setting.init_info_store",$goods_kind)}}" method="post"
      autocomplete="off"
      novalidate="novalidate">
    @csrf
    @include("component.input._hidden",["id"=>"tab_index","value"=>"product_bom"])
    @foreach($goods_kind_option["items"] as $goods_kind_select_item)
        @if(isset($goods_kind_select_item["selected"]) )
            <div class="w-100"></div>
            <div class="col-md-9" data-select2-id="119">
                @include("component.input.select2._select2",[
                       "id"=>"default_bom_warehouse_ids_".$goods_kind_select_item["value"],
                       "label"=>" انبارهای مجاز جهت تحویل    ".$goods_kind_select_item["text"],
                       "option"=>$bom_warehouse_ids_option[$goods_kind_select_item["value"]]["items"],
                       "class_col"=>""
               ])
            </div>

            <div class="w-100"></div>
            <div class="col-md-9" data-select2-id="119">
                @include("component.input.select2._select2",[
                       "id"=>"default_productive_consume_warehouse_ids_".$goods_kind_select_item["value"],
                       "label"=>" انبارهای مجاز مصرف کالای تولیدی    ".$goods_kind_select_item["text"],
                       "option"=>$bom_productive_consume_warehouse_option[$goods_kind_select_item["value"]]["items"],
                       "class_col"=>""
               ])
            </div>
            <div class="w-100"></div>
            <div class="col-md-9" data-select2-id="119">
                @include("component.input.select2._select2",[
                       "id"=>"default_sampling_consume_warehouse_ids_".$goods_kind_select_item["value"],
                       "label"=>" انبارهای مجاز مصرف کالای نمونه گیری ".$goods_kind_select_item["text"],
                       "option"=>$bom_sampling_consume_warehouse_option[$goods_kind_select_item["value"]]["items"],
                       "class_col"=>""
               ])
            </div>
        @endif
    @endforeach

    <div class="col-md-12">
        <br/>
        <a href="{{route("line_product_station.goods_kind.index")}}" class="btn btn-outline-dark">بازگشت</a>

        <button type="submit" class="btn btn-primary"> ذخیره</button>
    </div>
</form>
