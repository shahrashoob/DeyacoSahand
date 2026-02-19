<div class="dropdown drp-user show" style="display: inline-block">


    <a href="#" class="dropdown-toggle {{$goods_kind_id!=""?"text-danger    ":""}}" data-toggle="dropdown" >
        مشخصه کالا



        &nbsp;
    </a>
    <div class="dropdown-menu dropdown-menu-right profile-notification " style="padding: 10px; font-size: 10px">
        <form action="{{route($route)}}" method="post">
            @csrf
            @include("production.details_dashboard._hidden_inputs")
            <table>
                <tr>
                    <td>رسته کالایی:</td>
                    <td>
                        @include("component.input._select_simple",[
                    "id"=>"goods_kind_id",
                    "label"=>"رسته کالا",
                    "option"=>$goods_kind_option["items"],
                    "val"=>$goods_kind_option["value"],
                    "text"=>$goods_kind_option["text"],
                    "class"=>"",
                    "style"=>"width:100%",
                    ])
                    </td>
                </tr>
                <tr>
                    <td>مشخصه:  </td>
                    <td>
                        @include("component.input._select_simple",[
                   "id"=>"goods_kind_property_id",
                   "label"=>"مشخصه کالا",
                   "option"=>$property_option["items"],
                   "val"=>$property_option["value"],
                   "text"=>$property_option["text"],
                   "class"=>"",
                    "style"=>"width:100%",
                   ])
                    </td>
                </tr>
                <tr>
                    <td> برابر با:</td>
                    <td>
<div id="search_input">
                        <input type="text" id="search_goods_kind_property" name="search_goods_kind_property" style="width: 100%; margin-top: 10px; height: 20px; " value="{{$search_goods_kind_property}}">
</div>
                    </td>
                </tr>

                <tr>
                    <td>مشخصه:  </td>
                    <td>
                        @include("component.input._select_simple",[
                   "id"=>"goods_kind_property_id2",
                   "label"=>"مشخصه کالا",
                   "option"=>$property_option2["items"],
                   "val"=>$property_option2["value"],
                   "text"=>$property_option2["text"],
                   "class"=>"",
                    "style"=>"width:100%",
                   ])
                    </td>
                </tr>
                <tr>
                    <td> برابر با:</td>
                    <td>
                        <div id="search_input2">
                            <input type="text" id="search_goods_kind_property2" name="search_goods_kind_property2" style="width: 100%; margin-top: 10px; height: 20px; " value="{{$search_goods_kind_property2}}">
                        </div>
                    </td>
                </tr>

            </table>





            <button type="submit" class="btn btn-primary btn-sm btn_search">جستجو</button>
        </form>

    </div>

</div>