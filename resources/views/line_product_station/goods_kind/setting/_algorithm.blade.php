<form id="form3" action="{{route("line_product_station.goods_kind.setting.submit_algorithm_info",$goods_kind)}}" method="post"
      autocomplete="off"
      novalidate="novalidate">
    @csrf
    @include("component.input._hidden",["id"=>"tab_index","value"=>"algorithm"])

    <div class="w-100"></div>
    <div class="col-md-9" data-select2-id="119">

        <table class="table table-styling center">
            <tr>
                <th>نوع تامین</th>
                <th>الگوریتم صدور کارت تولید</th>
                <th>الگوریتم تخصیص کارت تولید</th>
            </tr>
            @foreach($supply_type_list as $supply_type)
                <tr>
                    <td style="width: 20%; vertical-align: middle">{{$supply_type->caption}}</td>
                    <td style="width: 40%">
                        @include("component.input._select",[
                            "id"=>"production_card_create_".$supply_type->id,
                            "option"=>$list_option[$supply_type->id]["production_card_create"]["items"],
                            "class_col"=>""
                    ])
                    </td>
                    <td style="width: 40%">
                        @include("component.input._select",[
                            "id"=>"production_card_allocation_".$supply_type->id,
                            "option"=>$list_option[$supply_type->id]["production_card_allocation"]["items"],
                            "class_col"=>""
                    ])
                    </td>
                </tr>
            @endforeach
        </table>

    </div>
    <div class="col-md-12">
        <br/>
        <a href="{{route("line_product_station.goods_kind.index")}}" class="btn btn-outline-dark">بازگشت</a>

        <button type="submit" class="btn btn-primary"> ذخیره</button>
    </div>
</form>

