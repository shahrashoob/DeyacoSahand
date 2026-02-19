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
                        "id"=>"shift_id",
                        "label"=>" فیلتر (شیفت کاری) ",
                        "text_white"=>1,
                        "option"=>$shift_option["items"],
                        "val"=>$shift_option["value"],
                        "text"=>$shift_option["text"],
                         "class_col"=>"col-md-2",
                        ])

                <div class="col-xs-4">
                    <button class="btn btn-primary" type="submit" style="    margin-top: 27px;"><i
                                class="fa fa-search"></i> جستجو
                    </button>

                </div>

            </div>
        </form>
    </div>
</div>

