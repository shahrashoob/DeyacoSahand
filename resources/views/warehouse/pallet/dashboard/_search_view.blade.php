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
                    @include("component.input._aotocomplet",[
                        "id"=>"status_id",
                        "label"=>" فیلتر (وضعیت) ",
                        "text_white"=>1,
                        "option"=>$status_option["items"],
                        "val"=>$status_option["value"],
                        "text"=>$status_option["text"],
                        ])
                </div>

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
