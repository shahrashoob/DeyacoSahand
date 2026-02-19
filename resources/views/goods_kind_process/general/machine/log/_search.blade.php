<div class="card text-white bg-secondary ">

    <div class="card-block">

        <form action="{{route($route,$machine)}}" method="post">
            <div class="row">
                @csrf

{{--                @include("component.input._text",[--}}
{{--                "id"=>"search",--}}
{{--                "label"=>" متن جستجو",--}}
{{--                "value"=>$search,--}}
{{--                "class_col"=>"col-md-4"--}}
{{--                ])--}}
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"user_id",
                        "label"=>"اقدام کننده",
                        "text_white"=>1,
                        "option"=>$user_option["items"],
                        "val"=>$user_option["value"],
                        "text"=>$user_option["text"],
                        ])
                </div>
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"operator_id",
                        "label"=>"اپراتور مسئول",
                        "text_white"=>1,
                        "option"=>$operator_option["items"],
                        "val"=>$operator_option["value"],
                        "text"=>$operator_option["text"],
                        ])
                </div>
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"production_status_id",
                        "label"=>"وضعیت تولید ماشین",
                        "text_white"=>1,
                        "option"=>$production_status_option["items"],
                        "val"=>$production_status_option["value"],
                        "text"=>$production_status_option["text"],
                        ])
                </div>
                <div class="col-md-2">
                    @include("component.input._aotocomplet",[
                        "id"=>"machine_event_type_id",
                        "label"=>"نوع رویداد",
                        "text_white"=>1,
                        "option"=>$machine_event_type_option["items"],
                        "val"=>$machine_event_type_option["value"],
                        "text"=>$machine_event_type_option["text"],
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
