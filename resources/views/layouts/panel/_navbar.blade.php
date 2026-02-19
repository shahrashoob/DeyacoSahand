<!-- pcoded-navbar  -->

<nav class="pcoded-navbar">
    <div class="navbar-wrapper">
        <div class="navbar-brand header-logo">
            <a href="index.html" class="b-brand">
                <div class="b-bg">
                    <i class="feather icon-circle"></i>
                </div>
                <span class="b-title"></span>
            </a>
            <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
        </div>
        <div class="navbar-content scroll-div">
            <ul class="nav pcoded-inner-navbar">

                @include("layouts._nav_icon",["icon"=>"fa fa-home","caption"=>"صفحه اصلی","link"=>"panel/home"])

{{--                @include("layouts._nav-item",['caption'=>'  مشتریان'])--}}


{{--                @include("layouts._nav_hasmenu",["icon"=>"fa fa-crosshairs ","caption"=>"مشتریان",--}}
{{--                       "submenu"=>[--}}
{{--                            ["link"=>"report/customer_list","caption"=>"لیست مشتریان"],--}}
{{--                            ["link"=>"panel/customer/add","caption"=>"مشتری جدید"],--}}

{{--                       ]--}}
{{--                ])--}}

                {{--                @include("layouts._nav_icon",["icon"=>"fa fa-stopwatch ","caption"=>" لیست دوره های ارزیابی","link"=>"hrm/report/project_list"])--}}

                @include("layouts._nav_icon",["icon"=>"feather icon-log-out","caption"=>"خروج","link"=>"logout"])


            </ul>
        </div>
    </div>
</nav>
<!-- /pcoded-navbar -->
