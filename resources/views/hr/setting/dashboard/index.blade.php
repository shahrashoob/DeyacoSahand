@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="col-sm-12">
                <h5>تنظیمات منابع انسانی</h5>

                <hr>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active show  text-uppercase" id="home-tab" data-toggle="tab"
                           href="#home"
                           role="tab" aria-controls="home" aria-selected="false">تنظیمات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link   text-uppercase" id="sms-tab"
                           data-toggle="tab" href="#sms"
                           role="tab"
                           aria-controls="profile" aria-selected="true">
                            تنظیمات پیامکی
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  text-uppercase" id="contact5-tab"
                           data-toggle="tab" href="#contact5"
                           role="tab"
                           aria-controls="contact5" aria-selected="false">ماژول های تحویل شیفت</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  text-uppercase" id="contact6-tab"
                           data-toggle="tab" href="#contact6"
                           role="tab"
                           aria-controls="contact6" aria-selected="false">بانک های طرف قرارداد</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  text-uppercase" id="contact7-tab"
                           data-toggle="tab" href="#contact7"
                           role="tab"
                           aria-controls="contact7" aria-selected="false">تنظیمات پیامکی همکاری با ما</a>
                    </li>


                </ul>
                <div class="tab-content " id="myTabContent">
                    <div class="tab-pane fade active show " id="home" role="tabpanel"
                         aria-labelledby="home-tab">
                       @include("hr.setting.dashboard._public_setting")
                    </div>
                    <div class="tab-pane fade " id="sms" role="tabpanel"
                         aria-labelledby="sms-tab">

                        @include("hr.setting.dashboard._sms_setting")

                    </div>
                    <div class="tab-pane fade " id="contact5" role="tabpanel"
                         aria-labelledby="profile-tab">

                        @include("hr.setting.dashboard._module_setting")

                    </div>
                    <div class="tab-pane fade " id="contact6" role="tabpanel"
                         aria-labelledby="bank-tab">

                        @include("hr.setting.dashboard._bank_setting")

                    </div>
                    <div class="tab-pane fade " id="contact7" role="tabpanel"
                         aria-labelledby="employment_notification_setting">

                        @include("hr.setting.dashboard._employment_notification_setting")

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
