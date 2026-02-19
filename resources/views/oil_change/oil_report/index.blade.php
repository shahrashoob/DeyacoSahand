@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت تعویض روغنی")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5> {{ $customer->shop_name }}

                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("oil_change.oil.report.show")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._select",["id"=>"report_type_id","lable"=>"انتخاب نوع گزارش",
"option"=>[
    ["value"=>1,"caption"=>"گزارش بر اساس شماره پلاک","selected"=>1],
    ["value"=>2,"caption"=>"گزارش بر اساس شماره موبایل"],

]
])
                            @include("component.input.datepicker._datepicker",["id"=>"start_date","lable"=>"از تاریخ "])

                            @include("component.input.datepicker._datepicker",["id"=>"end_date","lable"=>" تا تاریخ "])


                            <div class="col-md-12" style="text-align: center">
                                <br/>
                                <button type="submit" class="btn btn-primary btn-lg">مشاهده گزارش</button>
                                <a href="{{route("oil_change.home.index")}}" class="btn btn-dark btn-lg">بازگشت</a>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div>

            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script src="{{asset('assets/plugins/jquery-validation-1.11.1/localization/messages_fa2.js')}}"></script>

    <script>

        $('#form1').validate({
            rules: {
                "start_date_value":"required",
                "end_date_value":"required",

            }
        });

    </script>
@endsection
