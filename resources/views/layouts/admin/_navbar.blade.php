<!-- pcoded-navbar  -->
@php $user=\Auth::user();  @endphp

<nav class="pcoded-navbar navbar-{{env("NAVBAR_COLOR")}}">
    <div class="navbar-wrapper">
        <div class="navbar-brand header-logo">
            <a href="#" class="b-brand">
                <div class="b-bg">

                </div>
                <span class="b-title"></span>
            </a>
            <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
        </div>
        <div class="navbar-content scroll-div">
            <ul class="nav pcoded-inner-navbar">

                @include("layouts._nav_icon",["icon"=>"fa fa-home","caption"=>"صفحه اصلی","link"=>""])

                @php

                    $navs=\Auth::user()->getNavBars();

                @endphp


                @foreach ($menu_types as $item)

                    @if( isset($navs[$item->id]))
                        @if($item->id == 3100)
                            @include("layouts._nav_icon",["icon"=>"fa fas fa-comment","caption"=>$item->caption,"badge"=>\Auth::user()->seen_count,"link"=>route("hr.chat.index")])
                        @else
                            @include("layouts._nav_list",
                                    ["icon"=>$item->icon,
                                    "caption"=>$item->caption,
                                    "color"=>$item->color,
                                    "submenu"=>$navs[$item->id]
                                    ])
                        @endif
                    @endif

                @endforeach


                @include("layouts._nav_icon",["icon"=>"feather icon-log-out","caption"=>"خروج","link"=>"logout"])


            </ul>

        </div>
    </div>
</nav>
<!-- /pcoded-navbar -->
