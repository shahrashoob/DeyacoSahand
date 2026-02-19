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
                        "id"=>"priority_id",
                        "label"=>"فیلتر (اولویت) ",
                        "text_white"=>1,
                        "option"=>$priority_option["items"],
                        "val"=>$priority_option["value"],
                        "text"=>$priority_option["text"],
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
