<div class="card text-white bg-secondary ">

    <div class="card-block">

        <form action="{{route($route)}}" method="post">
            <div class="row">
                @csrf

                @include("component.input._text",[
                "id"=>"search",
                "label"=>" متن جستجو",
                "value"=>$search,
                "class_col"=>"col-md-3"
                ])


                <div class="col-md-3" id="product_type_option">
                    @include("component.input._select",[
                        "id"=>"status_id",
                        "label"=>"وضعیت ماشین",
                        "option"=>$status_option["items"],
                        "val"=>$status_option["items"],
                        "text"=>$status_option["items"],
                        "class_col"=>""
                        ])
                </div>
{{--                @if(isset($production_channel_type_option) && $production_channel_type_option)--}}
{{--                <div class="col-md-3" id="product_type_option">--}}
{{--                    @include("component.input._select",[--}}
{{--                        "id"=>"production_channel_type_id",--}}
{{--                        "label"=>"کانال تولید",--}}
{{--                        "option"=>$production_channel_type_option["items"],--}}
{{--                        "val"=>$production_channel_type_option["items"],--}}
{{--                        "text"=>$production_channel_type_option["items"],--}}
{{--                        "class_col"=>""--}}
{{--                        ])--}}
{{--                </div>--}}
{{--                &nbsp;@endif--}}
                <div class="col-xs-4">
                    <button class="btn btn-primary" type="submit" style="    margin-top: 27px;"><i
                            class="fa fa-search"></i> جستجو
                    </button>

                </div>

            </div>
        </form>
    </div>
</div>
