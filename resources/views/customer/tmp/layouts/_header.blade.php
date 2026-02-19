<!-- header -->

<header class="navbar pcoded-header navbar-expand-lg navbar-light" >
    <div class="m-header">
        <a class="mobile-menu" id="mobile-collapse1" href="#!"><span></span></a>

                <img href="{{asset('assets/images/logo.png')}}" style="width:30px">


            <span class="b-title">{{$setting["company_name"]->string_value??""}}</span>

    </div>
    <a class="mobile-menu" id="mobile-header" href="#!">
        <i class="feather icon-more-horizontal"></i>
    </a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li><a href="#!" class="full-screen" onclick="javascript:toggleFullScreen()"><i
                        class="feather icon-maximize"></i></a></li>

<li>
    {{$setting["company_name"]->string_value??""}}
</li>
        </ul>

        <ul class="navbar-nav ml-auto">

            <li>
                <div class="dropdown drp-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon feather icon-settings"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-notification">
                        <div class="pro-head">
                            <img src="#" class="img-radius" alt="">
                            <span>{{\Auth::user()->fullname()}} </span>
                            <a href="{{route("logout")}}" class="dud-logout" title="Logout">
                                <i class="feather icon-log-out"></i>
                            </a>
                        </div>
                        <ul class="pro-body">


                            <li><a href="{{route("logout","qr_link")}}" class="dropdown-item">
                                    <i class="feather icon-log-out"></i> خروج </a></li>

                            <li>
                                <a href="{{route("dashboard")}}" class="dropdown-item"> <i
                                        class="fa fa-language"></i> {{__("menu.language")}}: {{ __(app()->getLocale()) }} </a>
{{--                            @foreach($config_locales as $key=>$language)--}}
{{--                                <a href="{{route("language",$key)}}" class="">  {{ __($key) }}</a>--}}
{{--                                @endforeach--}}
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>
<!-- /header -->
