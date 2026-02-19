@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>تنظیمات داشبورد لحظه ای</h5>
                    </div>
                    <div class="card-block">
                        <form id="form1" action="{{route("utility.setting.update")}}" method="post"
                              novalidate="novalidate">
                            @csrf

                        <div class="row">


                            @include("utility.setting._radio_box",["key"=>"real_time_show_report_bar","label1"=>"بله","label0"=>"خیر"])

                            @include("utility.setting._radio_box",["key"=>"real_time_top_customer_show","label1"=>"بله","label0"=>"خیر"])
                            @include("utility.setting._radio_box",["key"=>"real_time_top_order_delay_show","label1"=>"بله","label0"=>"خیر"])

                            @include("component.input._number",["id"=>$values["real_time_top_order_delay_number"]->key,"lable"=>$values["real_time_top_order_delay_number"]->caption,"value"=>$values["real_time_top_order_delay_number"]->integer_value])
                            @include("component.input._number",["id"=>$values["real_time_top_customer_number"]->key,"lable"=>$values["real_time_top_customer_number"]->caption,"value"=>$values["real_time_top_customer_number"]->integer_value])
                            @include("component.input._number",["id"=>$values["real_time_top_customer_days"]->key,"lable"=>$values["real_time_top_customer_days"]->caption,"value"=>$values["real_time_top_customer_days"]->integer_value])


                        </div>
                            <div class="col-md-12" style="text-align: center" id="button_list">
                                <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary">
                                    ذخیره تغییرات
                                </button>

                            </div>
                        </form>
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
                        rules: {}
                    });
                </script>
@endsection

