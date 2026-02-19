<div class="col-sm-12 mb-3">
    <h5 class="mb-3"> ضایعات {{$product->fullCaption()}}
    </h5>

    <hr>


        <div class="accordion" id="accordionExample">
            @foreach($product->route()->where("active_status_id",1200)->get() as $route)

                <div class="card">
                    <div
                        class="card-header" {{$route->active_status_id!=1200 ? 'style=background:#1e3953!important':""}} >
                        <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route{{$route->id}}"
                                            aria-expanded="false" aria-controls="collapseOne" class="collapsed">

                                ضایعات همراه تولید در {{$route->caption}}


                            </a>
                        </h5>

                    </div>
                    <div class="multi-collapse collapse {{$route->code=="01"?"show":""}}" id="route{{$route->id}}"
                         style=""
                         data-parent="#accordionExample">
                        <br/>
                        <div class="col-md-12">
                            <div class="row">


                                    <div class="table-responsive">
                                        <table class="table table-styling center">
                                            <tr>
                                                <th>نوع ضایعات</th>
                                                <th>ضایعات</th>
                                                <th> در صدضایعات </th>
                                            </tr>
                                            @for($k=1;$k<= $max_waste;$k++)
                                            <tr>
                                                <td>{{$k}}</td>
                                                <td> {{$product_option_list["2_".$route->id."_".$k]["text"]}}</td>
                                                <td>{{$product_waste_percent["2_".$route->id."_".$k]}}</td>
                                            </tr>
                                            @endfor
                                        </table>
                                    </div>
{{--                                    <div class="col-md-3">--}}
{{--                                        @include("component.input._select",[--}}
{{--                                            "id"=>"waste_in_route_".$route->id."_".$k,--}}
{{--                                            "label"=>"ضایعات ".$k,--}}
{{--                                            "option"=>$product_option_list["2_".$route->id."_".$k]["items"],--}}
{{--                                            "val"=>$product_option_list["2_".$route->id."_".$k]["value"],--}}
{{--                                            "text"=>$product_option_list["2_".$route->id."_".$k]["text"],--}}
{{--                                            "class_col"=>""--}}
{{--                                            ])--}}
{{--                                    </div>--}}



                            </div>
{{--                            <div class="row">--}}
{{--                                @for($k=1;$k<= $max_waste;$k++)--}}
{{--                                    <div class="col-md-3">--}}
{{--                                        @include("component.input._number",[--}}
{{--                                            "id"=>"waste_in_route_".$route->id."_".$k."_percent",--}}
{{--                                            "label"=>"درصد ضایعات ".$k,--}}
{{--                                            "value"=>$product_waste_percent["2_".$route->id."_".$k],--}}
{{--                                            "class_col"=>""--}}
{{--                                            ])--}}
{{--                                    </div>--}}
{{--                                @endfor--}}


{{--                            </div>--}}
                        </div>

                        <br/>
                    </div>
                </div>

            @endforeach

            <div class="card">
                <div
                    class="card-header">
                    <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#consumed"
                                        aria-expanded="false" aria-controls="collapseOne" class="collapsed">

                            ضایعات در حین مصرف


                        </a>
                    </h5>

                </div>
                <div class="multi-collapse collapse " id="consumed"
                     style=""
                     data-parent="#accordionExample">

                    <br/>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-styling center">
                                    <tr>
                                        <th>نوع ضایعات</th>
                                        <th>ضایعات در حین مصرف</th>
                                    </tr>
                                    @for($k=1;$k<= $max_waste;$k++)
                                        <tr>
                                            <td>{{$k}}</td>
                                            <td> {{$product_option_list["1_0_".$k]["text"]??""}}</td>
                                        </tr>
                                    @endfor
                                </table>
                            </div>
{{--                            @for($k=1;$k<= $max_waste;$k++)--}}
{{--                                <div class="col-md-3">--}}
{{--                                    @include("component.input._select",[--}}
{{--                                "id"=>"waste_during_consumption_id_".$k,--}}
{{--                                "label"=>" ضایعات در حین مصرف  ".$k,--}}
{{--                                  "option"=>$product_option_list["1_0_".$k]["items"],--}}
{{--                                "val"=>$product_option_list["1_0_".$k]["value"],--}}
{{--                                "text"=>$product_option_list["1_0_".$k]["text"],--}}
{{--                                "class_col"=>""--}}
{{--                                ])--}}
{{--                                </div>--}}
{{--                            @endfor--}}
                        </div>
                    </div>

                    <br/>


                    <br/>
                </div>
            </div>
        </div>
    <br/>
      @include($view_path."_btn_list")



</div>

