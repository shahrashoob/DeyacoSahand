<div class="card text-white bg-secondary ">

    <div class="card-block">

        <form action="{{route($route,$machine)}}" method="post">
            <div class="row">
                @csrf
                <div class="w-100"></div>

                <div class="col-md-2" id="search_input">
                @include("component.input._text",[
                "id"=>"search",
                "label"=>" متن جستجو",
                "value"=>$search,
                "class_col"=>""
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
