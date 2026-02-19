<div class="card text-white bg-secondary ">

    <div class="card-block">

        <form action="{{route($route)}}" method="post">
            <div class="row">
                @csrf

                @include("component.input._text",[
                "id"=>"search",
                "label"=>" متن جستجو",
                "value"=>$search??"",
                "class_col"=>"col-md-2"
                ])

                &nbsp; @include("component.input.datepicker._datepicker",["id"=>"start_date","lable"=>"از تاریخ ", "value"=>$start_date_time, "class_col"=>"col-md-2"])

                @include("component.input.datepicker._datepicker",["id"=>"end_date","lable"=>" تا تاریخ ", "value"=>$end_date_time,"class_col"=>"col-md-2"])

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
