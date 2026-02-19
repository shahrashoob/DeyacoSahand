<div class="card">
    <div class="card-block  " style="padding: 5px">

        <form action="{{url("hrm/report_search")}}" method="post" >
            @csrf
            <input type="hidden" name="report_name" value="{{$report_name}}">
            <nav class="navbar m-b-10 p-10 ">
            <ul class="nav">
                @foreach($filter_items as $item)
                    @switch($item)
                        @case ("unit_id")
                            <li>

                        @include("component.input._aotocomplet",[
                            "id"=>$option["unit"]["id"],
                            "label"=>$option["unit"]["label"],
                            "val"=>$option["unit"]["value"],
                            "text"=>$option["unit"]["text"],
                            "option"=>$option["unit"]["items"]
                        ])

                        </li>
                            @break
                        @case ("role_id")
                            <li>

                        @include("component.input._aotocomplet",[
                            "id"=>$option["role"]["id"],
                            "label"=>$option["role"]["label"],
                            "val"=>$option["role"]["value"],
                            "text"=>$option["role"]["text"],
                            "option"=>$option["role"]["items"]
                        ])

                        </li>
                            @break
                        @case ("project_id")
                            <li>

                        @include("component.input._aotocomplet",[
                            "id"=>$option["project"]["id"],
                            "label"=>$option["project"]["label"],
                            "val"=>$option["project"]["value"],
                            "text"=>$option["project"]["text"],
                            "option"=>$option["project"]["items"]
                        ])

                    </li>
                        @break
                        @case ("education_id")
                            <li>

                        @include("component.input._aotocomplet",[
                            "id"=>$option["education"]["id"],
                            "label"=>$option["education"]["label"],
                            "val"=>$option["education"]["value"],
                            "text"=>$option["education"]["text"],
                            "option"=>$option["education"]["items"]
                        ])

                    </li>
                        @break
                        @case ("gender_id")
                            <li>

                        @include("component.input._aotocomplet",[
                            "id"=>$option["gender"]["id"],
                            "label"=>$option["gender"]["label"],
                            "val"=>$option["gender"]["value"],
                            "text"=>$option["gender"]["text"],
                            "option"=>$option["gender"]["items"]
                        ])

                    </li>

                        @break

                        @case ("current_activity_id")
                        <li>

                            @include("component.input._aotocomplet",[
                                "id"=>$option["current_activities"]["id"],
                                "label"=>$option["current_activities"]["label"],
                                "val"=>$option["current_activities"]["value"],
                                "text"=>$option["current_activities"]["text"],
                                "option"=>$option["current_activities"]["items"]
                            ])

                        </li>
                        @break

                        @case ("unit_level_id")
                        <li>

                            @include("component.input._aotocomplet",[
                                "id"=>$option["unit_level"]["id"],
                                "label"=>$option["unit_level"]["label"],
                                "val"=>$option["unit_level"]["value"],
                                "text"=>$option["unit_level"]["text"],
                                "option"=>$option["unit_level"]["items"]
                            ])

                        </li>
                        @break
                    @endswitch
                @endforeach
                <li>

                </li>
            </ul>


        </nav>

            <div >
                @if(count($filter_items)>0)
                    <button class="btn large btn-primary   m-t-30 " style="margin-right: 5px;" type="submit" id="btnSearch" >
                        <i class="fa fa-search "></i> مشاهده
                    </button>
                @endif

                @if(isset($info["buttons"]))
                    @foreach($info["buttons"] as $btn)
                        <a class="btn large btn-success   m-t-30 "  type="button" href="{{url($btn["url"])}}" >
                            <i class="fa {{$btn["icon"]}} "></i> {{$btn["label"]}}
                        </a>
                    @endforeach
                @endif
            </div>

        </form>

    </div>
</div>
