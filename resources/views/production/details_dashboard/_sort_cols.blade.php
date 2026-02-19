<div class="dropdown drp-user show" style="display: inline-block">


    <a href="#" class=" dropdown-toggle " data-toggle="dropdown"
       aria-expanded="true">
        مرتب سازی
        <i class="fa  fa-sort-alpha-down"></i>

    </a>
    <div class="dropdown-menu dropdown-menu-right profile-notification ">
        <form action="{{route($route)}}" method="post">
            @csrf
            @include("production.details_dashboard._hidden_inputs")

                <div  style="padding: 10px; font-size: 10px"> مرتب سازی بر اساس:
                    <br/>
                    @include("component.input._select_simple",[
                       "id"=>"order_by",
                       "label"=>"  مرتب سازی",
                       "text_white"=>1,
                       "option"=>$order_by_Option["items"],
                       "val"=>$order_by_Option["value"],
                       "text"=>$order_by_Option["text"],
                        "class"=>"",
                        "style"=>"",
                       ])
<br/>
                    <button type="submit" class="btn-primary  btn-sm btn_search">ثبت</button>
                </div>

        </form>

    </div>
</div>

