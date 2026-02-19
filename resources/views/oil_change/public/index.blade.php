<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> ای چال</title>
    <!-- boorstrap -->
    <link rel="stylesheet" type="text/css" href="{{asset("oil_change")}}/css/bootstrap.min.css">
    <!-- themify-icon.css -->
    <link rel="stylesheet" type="text/css" href="{{asset("oil_change")}}/css/themify-icons.css">
    <!-- animate.css -->
    <link rel="stylesheet" type="text/css" href="{{asset("oil_change")}}/css/animate.css">
    <!-- owl-carousel -->
    <link rel="stylesheet" type="text/css" href="{{asset("oil_change")}}/css/owl.carousel.css">
    <!-- video.min.css -->
    <link rel="stylesheet" type="text/css" href="{{asset("oil_change")}}/css/video.min.css">
    <!-- menu style -->
    <link rel="stylesheet" type="text/css" href="{{asset("oil_change")}}/css/menu.css">
    <!-- style -->
    <link rel="stylesheet" type="text/css" href="{{asset("oil_change")}}/css/style.css">
    <!-- responsive.css -->
    <link rel="stylesheet" type="text/css" href="{{asset("oil_change")}}/css/responsive.css">
    <link rel="stylesheet" href="{{asset('assets/fonts/fontiran/fontiran.css')}}">

</head>
<body>
<!-- Start of Header============================================= -->
<header>
    <div id="main-menu" class="main-menu-container tbg navbar-fixed-top">
        <div  class="main-menu">
            <div class="container">
                <div class="row">
                    <div class="navbar navbar-default" role="navigation">
                        <div class="container-fluid">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <div class="navbar-header">

                                <a class="navbar-brand text-uppercase" href="#"><img style="width: 100px" src="{{asset("oil_change")}}/pics/logo/logo.png" alt="logo"></a>
                            </div><!-- /.navbar-header -->
                            <!-- Collect the nav links, forms, and other content for toggling -->

                        </div><!-- /.container-fluid -->
                    </div><!-- /.navbar navbar-default -->
                </div><!-- /.row -->

            </div><!-- /.container -->
        </div><!-- /.full-main-menu -->
    </div><!-- #main-menu -->
    <!-- Main Menu end -->
</header> <!-- .cd-auto-hide-header -->
<!-- End of Header ============================================= -->
<section id="about" class="about-section">
    <div class="container">
        <div class="row section-content">
            <div class="about-section-content">
                <div class="row">

                    <!-- //col-sm-6 -->
                    <div class="col-md-12">
                        <div class="about-section-text">
                            <div class="section-title text-right pb50">
                                <h1 class="title deep-black pb40" style="text-align: center">سامانه  آی چال </h1>
                                <div  class="title-dec" style="text-align: center">
                                    <span>با آی چال دیگر نگران تاخیر در سرویس های خودروی خود نباشید</span>
                                </div>
                                <div style="text-align: center" >
                                    <br/>
                                    <a href="{{route("login")}}" >
                                        <img style="height: 60px" src="{{asset("oil_change")}}/img/login.png" alt="login">
                                    </a>
                                </div>
                            </div>
                            <!-- //section-title -->
                            <div class="download-store ul-li" style="text-align:right">
                                <br/>
                                <br/>

                            </div>
                        </div>
                        <!-- //about-section-text -->
                    </div>
                    <!-- //col-sm-6 -->
                </div>
            </div>
            <!-- //about-section-content -->
        </div><!-- /row -->
    </div><!-- /container -->
</section>
<!-- Start of About Section ============================================= -->

<!-- End of About Section ============================================= -->

<!-- Start of features section============================================= -->
{{--<section id="features" class="features-section">--}}
{{--    <div class="container">--}}
{{--        <div class="row section-content" style="padding-top: 150px; padding-bottom: 0px">--}}
{{--            <div class="features-content">--}}
{{--                <div class="row">--}}
{{--                    <div class="col-md-4">--}}
{{--                        <div class="features-text-icon text-center">--}}
{{--                            <div class="features-icon">--}}
{{--                                <i class="orange-gred ti-desktop"></i>--}}
{{--                            </div>--}}
{{--                            <!-- //icon -->--}}
{{--                            <div class="features-text mt25">--}}
{{--                                <div class="features-text-title pb10">--}}
{{--                                    <h3 class="deep-black">کاربری آسان </h3>--}}
{{--                                </div>--}}
{{--                                <div class="features-text-dec">--}}
{{--                                    <span>ثبت سرویس در کمتر از یک دقیقه</span>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <!-- //text -->--}}
{{--                        </div><!-- // features-text-icon -->--}}
{{--                    </div>--}}
{{--                    <!-- // col-sm-4 -->--}}
{{--                    <div class="col-md-4">--}}
{{--                        <div class="features-text-icon text-center">--}}
{{--                            <div class="features-icon">--}}
{{--                                <i class="orange-gred ti-dashboard"></i>--}}
{{--                            </div>--}}
{{--                            <!-- //icon -->--}}
{{--                            <div class="features-text mt25">--}}
{{--                                <div class="features-text-title pb10">--}}
{{--                                    <h3 class="deep-black"> اطلاع رسانی هوشمند</h3>--}}
{{--                                </div>--}}
{{--                                <div class="features-text-dec">--}}
{{--                                    <span>اطلاع از وضعیت سرویس های خودرو به صورت آنلاین</span>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <!-- //text -->--}}
{{--                        </div><!-- // features-text-icon -->--}}
{{--                    </div>--}}
{{--                    <!-- // col-sm-4 -->--}}
{{--                    <div class="col-md-4">--}}
{{--                        <div class="features-text-icon text-center">--}}
{{--                            <div class="features-icon">--}}
{{--                                <i class="orange-gred ti-settings"></i>--}}
{{--                            </div>--}}
{{--                            <!-- //icon -->--}}
{{--                            <div class="features-text mt25">--}}
{{--                                <div class="features-text-title pb10">--}}
{{--                                    <h3 class="deep-black">محاسبه دقیق</h3>--}}
{{--                                </div>--}}
{{--                                <div class="features-text-dec">--}}
{{--                                    <span>محاسبه دقیق عملکرد هر سرویس</span>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <!-- //text -->--}}
{{--                        </div><!-- // features-text-icon -->--}}
{{--                    </div>--}}
{{--                    <!-- // col-sm-4 -->--}}
{{--                </div><!-- /section-row -->--}}
{{--            </div><!-- /features-content -->--}}
{{--        </div><!-- /row -->--}}
{{--    </div><!-- /container -->--}}
{{--</section>--}}
<!-- End of features section ============================================= -->

<!-- Start of some extra features ============================================= -->

<!-- End of some extra features ============================================= -->

<!-- Start of footer section ============================================= -->

<!-- End of footer section ============================================= -->

<!--  Js Library -->
<script type="text/javascript" src="{{asset("oil_change")}}/js/jquery-2.1.4.min.js"></script>
<!-- Include  for bootstrap -->
<script type="text/javascript" src="{{asset("oil_change")}}/js/bootstrap.min.js"></script>
<!-- Include Owl-carousel -->
<script type="text/javascript" src="{{asset("oil_change")}}/js/owl.carousel.min.js"></script>
<!-- Include jquery.magnific-popup.min.js-->
<script type="text/javascript" src="{{asset("oil_change")}}/js/jquery.magnific-popup.min.js"></script>
<!-- Include script.js-->
<script type="text/javascript" src="{{asset("oil_change")}}/js/script.js"></script>

</body><!-- This template has been downloaded from Webrubik.com -->
</html>
