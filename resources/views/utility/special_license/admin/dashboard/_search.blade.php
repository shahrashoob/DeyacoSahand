<div class="card text-white bg-secondary ">

    <div class="card-block">

        <form action="{{route($route)}}" method="post">
            <div class="row">
                @csrf

                @include("component.input._text",[
                "id"=>"search",
                "label"=>" متن جستجو",
                "value"=>$search,
                "class_col"=>"col-md-4"
                ])
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"status_id",
                        "label"=>"فیلتر (وضعیت) ",
                        "text_white"=>1,
                        "option"=>$status_Option["items"],
                        "val"=>$status_Option["value"],
                        "text"=>$status_Option["text"],
                        ])
                </div>
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"user_id",
                        "label"=>"فیلتر (افراد) ",
                        "text_white"=>1,
                        "option"=>$worker_option["items"],
                        "val"=>$worker_option["value"],
                        "text"=>$worker_option["text"],
                        ])
                </div>
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"special_license_type_id",
                        "label"=>"فیلتر (نوع مجوز) ",
                        "text_white"=>1,
                        "option"=>$special_license_type_option["items"],
                        "val"=>$special_license_type_option["value"],
                        "text"=>$special_license_type_option["text"],
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
                <div class="w-100"></div>
                @include("component.input..datepicker._datepicker",["id"=>"start_datetime",'label'=>"از تاریخ ","value"=>$start_datetime??null,"class_col"=>"col-md-2"])
                @include("component.input..datepicker._datepicker",["id"=>"end_datetime",'label'=>"تا تاریخ ","value"=>$end_datetime??null,"class_col"=>"col-md-2"])

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
