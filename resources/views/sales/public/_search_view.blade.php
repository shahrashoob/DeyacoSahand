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
                "id"=>"product_id_in_search",
                "label"=>"نام / کد کالا",
                "value"=>$product_id_in_search,
                "class_col"=>"col-md-2"
                ])
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"waiting_status_id",
                        "label"=>"فیلتر (وضعیت سفارش) ",
                        "text_white"=>1,
                        "option"=>$waiting_status_option["items"],
                        "val"=>$waiting_status_option["value"],
                        "text"=>$waiting_status_option["text"],
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
                        "id"=>"leading_permission_status_id",
                        "label"=>"مجوز بارگیری ",
                        "text_white"=>1,
                        "option"=>$leading_permission_status_option["items"],
                        "val"=>$leading_permission_status_option["value"],
                        "text"=>$leading_permission_status_option["text"],
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
{{--                            <a href="{{route('wh.print.request_current_orders')}}"` class="btn btn-info" style="    margin-top: 27px;"><i--}}
{{--                                    class="fa fa-print"></i> پرینت--}}
{{--                            </a>--}}
{{--                            <a href="{{route('wh.print.request_current_orders')}}"` class="btn btn-info" style="    margin-top: 27px;"><i--}}
{{--                                    class="fa fa-print"></i> ذخیره--}}
{{--                            </a>--}}
                </div>




            </div>
        </form>
    </div>
</div>
