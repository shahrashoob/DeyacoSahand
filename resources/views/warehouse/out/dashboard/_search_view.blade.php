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
                @include("component.input._text",[
                "id"=>"search_order_code",
                "label"=>"شماره سفارش",
                "value"=>$search_order_code,
                "class_col"=>"col-md-2"
                ])

                @include("component.input._text",[
                "id"=>"search_exist_form",
                "label"=>"شماره برگ خروج",
                "value"=>$search_exist_form,
                "class_col"=>"col-md-2"
                ])
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"status_id",
                        "label"=>" فیلتر (وضعیت درخواست) ",
                        "text_white"=>1,
                        "option"=>$status_option["items"],
                        "val"=>$status_option["value"],
                        "text"=>$status_option["text"],
                        ])
                </div>

                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"exit_form_status_id",
                        "label"=>"فیلتر (وضعیت برگ خروج) ",
                        "text_white"=>1,
                        "option"=>$exit_form_status_option["items"],
                        "val"=>$exit_form_status_option["value"],
                        "text"=>$exit_form_status_option["text"],
                        ])
                </div>
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"warehouse_id",
                        "label"=>" فیلتر انبار ",
                        "text_white"=>1,
                        "option"=>$warehouse_option["items"],
                        "val"=>$warehouse_option["value"],
                        "text"=>$warehouse_option["text"],
                        ])
                </div>


                    @include("component.input._select",[
                        "id"=>"prf_type_id",
                        "label"=>" فیلتر نوع درخواست ",
                        "text_white"=>1,
                        "option"=>$prf_type_option["items"],
                        "val"=>$prf_type_option["value"],
                        "text"=>$prf_type_option["text"],
                        "class_col"=>"col-md-2"
                        ])


                    @include("component.input._select",[
                        "id"=>"loading_status_id",
                        "label"=>" فیلتر مجوز بارگیری ",
                        "text_white"=>1,
                        "option"=>$loading_status_option["items"],
                        "val"=>$loading_status_option["value"],
                        "text"=>$loading_status_option["text"],
                        "class_col"=>"col-md-2"
                        ])

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
