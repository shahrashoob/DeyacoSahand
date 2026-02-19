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

                    @include("component.input._select",[
                        "id"=>"waiting_status_id",
                        "label"=>" فیلتر (وضعیت) ",
                        "text_white"=>1,
                        "option"=>$waiting_status_option["items"],
                        "val"=>$waiting_status_option["value"],
                        "text"=>$waiting_status_option["text"],
                         "class_col"=>"col-md-2",
                        ])
                @include("component.input._select",[
                                   "id"=>"event_id",
                                   "label"=>" فیلتر (رویداد) ",
                                   "text_white"=>1,
                                   "option"=>$event_option["items"],
                                   "val"=>$event_option["value"],
                                   "text"=>$event_option["text"],
                                    "class_col"=>"col-md-2",
                                   ])
                @include("component.input._select",[
                              "id"=>"user_id",
                              "label"=>"اقدام کننده ",
                              "text_white"=>1,
                              "option"=>$worker_option["items"],
                              "val"=>$worker_option["value"],
                              "text"=>$worker_option["text"],
                               "class_col"=>"col-md-2",
                              ])
            </div>
            <div class="row">
                @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", ["id"=>"start_date",'label'=>"از تاریخ", "value"=>$start_date,  "class_col"=>"col-md-2",])

                @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", ["id"=>"end_date", 'label'=>"تا تاریخ ", "value"=>$end_date,  "class_col"=>"col-md-2",])

                <div class="col-xs-4">
                    <button class="btn btn-primary" type="submit" style="    margin-top: 27px;"><i
                                class="fa fa-search"></i> جستجو
                    </button>

                </div>

            </div>
        </form>
    </div>
</div>

