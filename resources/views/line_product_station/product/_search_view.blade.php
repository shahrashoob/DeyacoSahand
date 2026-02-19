<div class="card text-white bg-secondary ">

    <div class="card-block">

        <form action="{{route($route)}}" method="post">
            <div class="row">
                @csrf

                @include("component.input._text",[
                "id"=>"search",
                "label"=>" متن جستجو",
                "value"=>$search,
                "class_col"=>"col-md-2"
                ])
                <div class="col-md-2">
                    @include("component.input._select",[
                        "id"=>"goods_kind_id",
                        "label"=>"رسته کالا",
                        "option"=>$goods_kind_option["items"],
                        "val"=>$product->goods_kind->id??"",
                        "text"=>$product->goods_kind->caption??"",
                        "class_col"=>""
                        ])
                </div>
                <div class="col-100"></div>
                <div class="col-md-2" id="search_property">
                    @include("component.input._select",[
                                           "id"=>"property_id",
                                           "label"=>"مشخصه کالا",
                                           "option"=>$property_option["items"],
                                           "val"=>$property_option["value"],
                                           "text"=>$property_option["text"],
                                           "class_col"=>""
                                           ])
                </div>
                <div class="col-md-2">
                    @include("component.input._select",[
                                          "id"=>"property_value_id",
                                           "label"=>"محتوای مشخصه",
                                          "option"=>$property_value_option["items"],
                                          "val"=>$property_value_option["value"],
                                          "text"=>$property_value_option["text"],
                                          "class_col"=>""
                                          ])
                    @include("component.input._text",[
                        "id"=>"property_value",
                        "label"=>"محتوای مشخصه",
                        "value"=>$property_value,
                        "class_col"=>""
                        ])
                </div>

                @if(isset($algorithm_option))
                    <div class="col-md-2">
                        @include("component.input._select",[
                            "id"=>"product_planing_algorithm_id",
                            "label"=>"الگوریتم برنامه ریزی",
                            "text_white"=>1,
                            "option"=>$algorithm_option["items"],
                            "val"=>$algorithm_option["value"],
                            "text"=>$algorithm_option["text"],
                             "class_col"=>""
                            ])
                    </div>
                    &nbsp;
                @endif
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"order_by",
                        "label"=>"  مرتب سازی",
                        "text_white"=>1,
                        "option"=>$order_by_Option["items"],
                        "val"=>$order_by_Option["value"],
                        "text"=>$order_by_Option["text"],
                        ])
                </div>
                <div class="col-xs-4">
                    <button class="btn btn-primary" type="submit" style="    margin-top: 27px;"><i
                                class="fa fa-search"></i> جستجو
                    </button>

                </div>

            </div>
        </form>
    </div>
</div>

