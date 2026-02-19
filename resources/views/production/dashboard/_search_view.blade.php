<div class="card text-white bg-secondary ">

    <div class="card-block">

        <form action="{{route($route)}}" method="post">
            <div class="row">
                @csrf
                <div class="w-100"></div>
                <div class="col-md-2">
                    @include("component.input._select",[
                        "id"=>"goods_kind_id",
                        "label"=>"رسته کالا",
                        "option"=>$goods_kind_option["items"],
                        "val"=>$goods_kind_option["value"],
                        "text"=>$goods_kind_option["text"],
                        "class_col"=>""
                        ])
                </div>
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
                @if(isset($production_channel_type_show_in_production_dashboard) && $production_channel_type_show_in_production_dashboard)
                    <div class="col-md-2" ">
                        @include("component.input._text",[
                        "id"=>"search_production_channel_type",
                        "label"=>" جستجوی کانال",
                        "value"=>$search_production_channel_type,
                        "class_col"=>""
                        ])
                    </div>
                @endif
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
                    @if(\Auth::user()->checkButtonPermission("production.export.production500"))
                        <a href="{{route("production.export.production_500")}}" class="btn btn-info" type="submit"
                           style="    margin-top: 27px;"><i
                                    class="fa fa-search"></i> دانلود اکسل
                        </a>
                    @endif
                </div>


            </div>
        </form>
    </div>
</div>
