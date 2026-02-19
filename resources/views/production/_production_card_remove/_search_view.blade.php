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
                <div class="col-md-3">
                    @include("component.input._aotocomplet",[
                        "id"=>"order_by",
                        "label"=>"  مرتب سازی بر اساس",
                        "text_white"=>1,
                        "option"=>$orber_by_Option["items"],
                        "val"=>$orber_by_Option["value"],
                        "text"=>$orber_by_Option["text"],
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
