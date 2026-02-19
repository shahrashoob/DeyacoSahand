@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="col-sm-12">
                <h5>تنظیمات فروش</h5>

                <hr>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active show  text-uppercase" id="home-tab" data-toggle="tab"
                           href="#home"
                           role="tab" aria-controls="home" aria-selected="false">گروه بندی کالاها در نمایش به مشتری</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link   text-uppercase" id="planing-tab"
                           data-toggle="tab" href="#planing"
                           role="tab"
                           aria-controls="profile" aria-selected="true">
                            وضعیت های مجاز برنامه ریزی

                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link   text-uppercase" id="sms-tab"
                           data-toggle="tab" href="#sms"
                           role="tab"
                           aria-controls="planing" aria-selected="true">
                            جمع فاکتورهای رسمی و غیررسمی
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link   text-uppercase" id="series-tab"
                           data-toggle="tab" href="#series"
                           role="tab"
                           aria-controls="series" aria-selected="true">
                            سری فاکتور ها
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link  show" id="sale-tab" data-toggle="pill"
                           href="#sale" role="tab" aria-controls="sale"
                           aria-selected="false">تنظیمات عمومی</a>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link  " id="pre_factor-tab" data-toggle="pill"
                           href="#pre_factor" role="tab" aria-controls="sale"
                           aria-selected="false">پیش فاکتور</a>
                    </li>



                </ul>
                <div class="tab-content " id="myTabContent">
                    <div class="tab-pane fade active show " id="home" role="tabpanel"
                         aria-labelledby="home-tab">
                        @include("sales.setting._product_group")
                    </div>
                    <div class="tab-pane fade " id="sms" role="tabpanel"
                         aria-labelledby="sms-tab">

                        @include("sales.setting._formal_status")
                    </div>
                    <div class="tab-pane fade " id="planing" role="tabpanel"
                         aria-labelledby="planing-tab">

                        @include("sales.setting._planing_status")
                    </div>
                    <div class="tab-pane fade " id="series" role="tabpanel"
                         aria-labelledby="series-tab">
                        @include("sales.setting._sale_series")



                    </div>
                    <div class="tab-pane fade " id="sale" role="tabpanel"
                         aria-labelledby="sale-tab">
                        @include("sales.setting._sale")



                    </div>
                    <div class="tab-pane fade " id="pre_factor" role="tabpanel"
                         aria-labelledby="pre_factor-tab">
                        @include("sales.setting._pre_factor")



                    </div>

                </div>
            </div>
        </div>




    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('form').validate({
            rules: {

            }
        });
    </script>
@endsection

