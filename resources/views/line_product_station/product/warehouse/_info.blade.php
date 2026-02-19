<div class="row">
    <div class="col-sm-12">
        <form id="form1" action="{{route($route_path."submit",[$product,$product_creation_process])}}" method="post"
              autocomplete="off"
              novalidate="novalidate">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    نوع انبارش:
                    @foreach($warehouse_storage_types as $item)
                        <br/>
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        @include("component.input._checkbox_simple",["id"=>"warehouse_storage_type_".$item->id,"label"=>$item->caption,"checked"=>isset($product_warehouse_storage_type[$item->id])])

                    @endforeach
                </div>

                <div class="w-100"><br/></div>
                @include("component.input._number",["id"=>"min_inventory",'label'=>" حداقل موجودی (نقطه سفارش)","value"=>$product->min_inventory])
                @include("component.input._number",["id"=>"max_inventory",'label'=>"حداکثر موجودی  ","value"=>$product->max_inventory])

                @if($product->supply_type_id == 1)
                <div class="col-md-6">
                    @include("component.input._select",[
                        "id"=>"default_packing_type_id",
                        "label"=>" بسته بندی پیش فرض تولید ",
                        "option"=>$packing_type_option["items"],
                        "val"=>$product->default_packing_type_id??"",
                        "text"=>$product->default_packing_type->caption??"",
                        "class_col"=>""
                        ])
                </div>
                @endif
                <div class="w-100"><br/></div>
                <div id="div_reservoir" class="col-md-12">
                    <div class="row">
                        @if(count($reservoir_list)==0)
                            <div class="col-md-12"> هیچ مخزنی تاکنون تعریف نشده است.</div>
                        @endif
                        @foreach($reservoir_list as $reservoir)
                            <div class="col-md-2">
                                <input
                                        type="checkbox" id="reservoir[{{$reservoir->id}}"
                                        name="reservoir[{{$reservoir->id}}]"
                                        @if(in_array($reservoir->id,$product_reservoir)) checked="checked" @endif
                                /> {{$reservoir->caption}}
                            </div>
                        @endforeach
                    </div>
                    <div class="w-100"><br/></div>
                </div>

                @include("component.input._radio_box01",["id"=>"product_have_specific_location","label"=>"آیا کالا محل مشخصی در انبار دارد؟","value"=>$product->product_have_specific_location])


                {{--                <div class="col-md-9" data-select2-id="119">--}}

                {{--                    @include("component.input.select2._select2",[--}}
                {{--                   "id"=>"warehouse_shelving_ids",--}}
                {{--                   "label"=>"لیست قفسه های مجاز کالا در انبار",--}}
                {{--                   "option"=>$warehouse_shelving_option["items"],--}}
                {{--                   "class_col"=>""--}}
                {{--                   ])--}}

                {{--                </div>--}}

                @include("line_product_station.product.warehouse._warehouse_shelving_list")

            </div>
            <br/>
            <br/>
            @include($view_path."_btn_list")

        </form>
    </div>

</div>
<script>

    $("#warehouse_storage_type_3").change(function () {
        change_storage();
    })
    $("#product_have_specific_location_0,#product_have_specific_location_1").change(function () {
        change_product_have_specific_location();
    })

    function change_storage() {
        if ($("#warehouse_storage_type_3").is(":checked")) {
            $("#div_reservoir").css("display", "")
        } else {
            $("#div_reservoir").css("display", "none")
        }
    }

    function change_product_have_specific_location() {
        if ($("#product_have_specific_location_1").is(":checked")) {
           $("#warehouse_shelving_list").css("display","")
        } else {
            $("#warehouse_shelving_list").css("display","none")
        }
    }

    change_storage();
    change_product_have_specific_location();
</script>
