<div class="card text-white bg-secondary ">

    <div class="card-block">

        <form action="{{route($route)}}" method="post">
            <div class="row">
                @csrf

                <div class="col-md-2">
                    @include("component.input._select",[
                        "id"=>"goods_kind_property_id",
                        "label"=>"مشخصه کالا",
                        "option"=>$property_option["items"],
                        "val"=>$property_option["value"],
                        "text"=>$property_option["text"],
                        "class_col"=>""
                        ])
                </div>
                <div class="col-md-2" id="search_input">
                @include("component.input._text",[
                "id"=>"search",
                "label"=>" متن جستجو",
                "value"=>$search,
                "class_col"=>""
                ])
                </div>

                <div class="col-md-2" id="search_input">
                    @include("component.input._text",[
                    "id"=>"order_search",
                    "label"=>"شماره سفارش",
                    "value"=>$order_search,
                    "class_col"=>""
                    ])
                </div>
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"waiting_status_id",
                        "label"=>" فیلتر (وضعیت) ",
                        "text_white"=>1,
                        "option"=>$waiting_status_option["items"],
                        "val"=>$waiting_status_option["value"],
                        "text"=>$waiting_status_option["text"],
                        ])
                </div>

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

                &nbsp;
                &nbsp;
                <div class="col-xs-4">
                    <button class="btn btn-primary" type="submit" style="    margin-top: 27px;"><i
                            class="fa fa-search"></i> جستجو
                    </button>

                </div>


            </div>
        </form>
    </div>
</div>
