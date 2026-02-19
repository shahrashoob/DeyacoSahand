

    <div class="card">
        <div class="card-header">
            <h5>جستجو  </h5>
        </div>
        <div class="card-block pb-0">

            <form action="{{route($route,[$order,$goods_kind_property_value])}}" method="post">
                <div class="row">
                    @csrf

{{--                    @include("component.input._aotocomplet2",[--}}
{{--                    "id"=>"goods_kind_id",--}}
{{--                    "label"=>"رسته کالایی",--}}
{{--                    "text_white"=>1,--}}
{{--                    "option"=>$goods_kind_option["items"],--}}
{{--                    "val"=>$goods_kind_option["value"],--}}
{{--                    "text"=>$goods_kind_option["text"],--}}
{{--                    "class_col"=>"col-md-3"--}}
{{--                    ])--}}
{{--                    @include("component.input._aotocomplet2",[--}}
{{--                    "id"=>"product_type_id",--}}
{{--                    "label"=>"گروه کالایی",--}}
{{--                    "text_white"=>1,--}}
{{--                    "option"=>$product_type_option["items"],--}}
{{--                    "val"=>$product_type_option["value"],--}}
{{--                    "text"=>$product_type_option["text"],--}}
{{--                    "class_col"=>"col-md-3"--}}
{{--                    ])--}}
                    @include("component.input._text",[
                    "id"=>"search",
                    "label"=>" متن جستجو",
                    "value"=>$search,
                    "class_col"=>"col-md-3"
                    ])

                    @include("component.input._aotocomplet2",[
                        "id"=>"order_by",
                        "label"=>"  مرتب سازی بر اساس",
                        "text_white"=>1,
                        "option"=>$order_by_Option["items"],
                        "val"=>$order_by_Option["value"],
                        "text"=>$order_by_Option["text"],
                        "class_col"=>"col-md-3"
                        ])

                    <div class="col-sm-12" >
                        <button class="btn btn-primary" type="submit" ><i
                                class="fa fa-search"></i> جستجو
                        </button><br/>
                        <br/>
                    </div>




                </div>
            </form>

        </div>
    </div>

