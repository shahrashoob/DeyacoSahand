<!-- pcoded-navbar  -->
@php $user=\Auth::user();  @endphp

<nav class="pcoded-navbar navbar-{{env("NAVBAR_COLOR")}}">
    <div class="navbar-wrapper">
        <div class="navbar-brand header-logo">
            <a href="#" class="b-brand">
                <div class="b-bg">

                </div>
                <span class="b-title" style="font-size: 12px">{{\Auth::user()->fullname()}}</span>
            </a>
            <a class="mobile-menu" id="mobile-collapse" href="#!"><span> </span></a>
        </div>
        <div class="navbar-content scroll-div">
            <ul class="nav pcoded-inner-navbar">

                @include("layouts._nav_icon",["icon"=>"fa fa-globe","caption"=> __("menu.kamatex") ,"link"=>"index"])
                @include("layouts._nav_icon",["icon"=>"fa fa-home","caption"=> __("menu.logintodashboard") ,"link"=>"dashboard"])


                {{--                        @include("layouts._nav_list",--}}
                {{--                                ["icon"=>$item->icon,--}}
                {{--                                "caption"=>$item->caption,--}}
                {{--                                "color"=>$item->color,--}}
                {{--                                "submenu"=>$navs[$item->id]--}}
                {{--                                ])--}}



                @include("layouts._nav_icon",["icon"=>"feather icon-log-out","caption"=>__("logout"),"link"=>route("logout","qr_link")])


            </ul>

        </div>
    </div>
</nav>
<!-- /pcoded-navbar -->
