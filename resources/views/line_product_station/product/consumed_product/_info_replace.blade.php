<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5> تغییر کالای مصرفی برای{{$product->fullCaption()}} </h5>
            </div>
            <div class="card-block">

                <form id="form1" action="{{route($route_path."store_replace",[$consumed_product,$product,$product_creation_process])}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate">
                    @csrf
                    <div class="row">
                        <div class="alert alert-warning">

                            شما در حال جابجایی ماده اولیه (
                            {{$consumed_product->material->fullCaption()}}
                            ) با یک ماده اولیه جدید هستید، در صورت تایید تمامی مواد اولیه در کالای مصرفی و ردیف های BOM انتخاب شده در جدول زیر جایگزین می شود.
                        </div>
                        <div class="col-md-6">
                            @include("component.input._aotocomplet2",[
                                "id"=>"new_material_id",
                                "label"=>" کالای مصرفی جدید ",
                                "option"=>$product_option["items"],
                                "val"=>$product_option["value"],
                                "text"=>$product_option["text"],
                                "class_col"=>""
                                ])
                        </div>
                        <div class="col-md-12">

                            @include("line_product_station.product.consumed_product._bom_item_for_replace")
                        </div>
                    </div>

                    @include($view_path."_btn_back")

                    <button type="submit" class="btn btn-primary"> تایید جابجایی</button>

                </form>

            </div>
        </div>
    </div>
</div>
