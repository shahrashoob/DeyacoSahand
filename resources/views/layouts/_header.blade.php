<!-- header -->

<header class="navbar pcoded-header navbar-expand-lg navbar-light">
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
            @php
                $client_buy = isset($post_user) && $post_user->checkButtonPermission("accounting.client.buy.index") ;
            @endphp
            @if(isset($client_buy) && $client_buy && (!isset($header_client_buy) || $header_client_buy))
                <li style="line-height:15px" id="header_client_buy">
                    @if($setting["credit"]->integer_value < $setting["min_of_charge_for_send_sms"]->integer_value)
                        <div class="spinner-grow text-danger" role="status">
                            <span class="sr-only"></span>
                        </div>
                    @endif
                    @if($setting["credit"]->integer_value <=  $setting["min_of_charge_for_reminder"]->integer_value && $setting["credit"]->integer_value >  $setting["min_of_charge_for_send_sms"]->integer_value)
                        <div class="spinner-grow text-warning" role="status">
                            <span class="sr-only"></span>
                        </div>
                    @endif
                    <button type="button" class="btn btn-outline-dark btn-sm" style="width: 220px; " title="">

                        اعتبار
                        حساب: {{number_format($setting["credit"]->integer_value??0)}} ریال
                    </button>

                    <a href="{{route("accounting.client.buy.index")}}" class="btn btn-success btn-sm  text-white"
                       style="width: 220px"
                       title="افزایش اعتبار">
                        افزایش اعتبار (انلاین)
                    </a>

                </li>
                @if(

                $setting["credit"]->integer_value <=  $setting["min_of_charge_for_payment"]->integer_value

                    &&
                    !in_array(Route::current()->getName(),["accounting.client.buy.index","accounting.client.buy.show"])
                    )
                    <script>
                        window.location = '{{route("accounting.client.buy.index")}}'
                    </script>
                @endif
            @endif
            <li>
                <div class="dropdown drp-user">


                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        {{jdate( \Carbon\Carbon::now()->timestamp )->format( 'H:i' )}}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-notification">
                        <div class="pro-head">

                            <span>  {{jdate( \Carbon\Carbon::now()->timestamp )->format( ' l d F Y' )}} </span>

                        </div>

                    </div>
                </div>
            </li>
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


                            <li><a href="{{route("logout")}}" class="dropdown-item">
                                    <i class="feather icon-log-out"></i> خروج </a></li>
                            <li>
                                <a href="{{route("change_pass")}}" class="dropdown-item"> <i
                                            class="fa fa-key"></i> تغییر کلمه عبور </a>
                            </li>
                            <li>
                                <a href="{{route("utility.printer.select_default_printer")}}" class="dropdown-item"> <i
                                            class="fa fa-print"></i> تغییر پرینتر پیش فرض </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>
<!-- /header -->
