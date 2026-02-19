@extends('layouts.admin._master')
@section("page_header_title","گزارش 1014 - گزارش حضور و غیاب پرسنل")

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>گزارش 1014 - گزارش حضور و غیاب پرسنل</h5>
                </div>

                <div class="card-block">
                    <form id="form1" autocomplete="off" action="{{route("report.1014.submit")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    @include("component.input.datepicker._datepicker",["id"=>"start_date","lable"=>"از تاریخ ","class_col"=>"col-md-10"])


                                    @include("component.input.datepicker._datepicker",["id"=>"end_date","lable"=>" تا تاریخ ","class_col"=>"col-md-10"])


                                    <div class="col-md-10">
                                        @include("component.input._select",[
                                            "id"=>"from_user_id",
                                            "label"=>"از فرد  ",
                                            "option"=>$worker_option["items"],
                                            "val"=>$worker_option["value"],
                                            "text"=>$worker_option["text"],
                                            "class_col"=>""
                                            ])
                                    </div>
                                    <div class="w-100"><br/></div>
                                    <div class="col-md-10">
                                        @include("component.input._select",[
                                            "id"=>"to_user_id",
                                            "label"=>"تا فرد  ",
                                            "option"=>$worker_option["items"],
                                            "val"=>$worker_option["value"],
                                            "text"=>$worker_option["text"],
                                            "class_col"=>""
                                            ])
                                    </div>

                                    <input type="hidden" id="leading_false" value="1">

                                </div>
                            </div>

                        </div>
                        <br/>
                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary"> مشاهده گزارش </button>

                    </form>
                </div>

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
    <script>
        $('#form1').validate({
            rules: {
                "start_date_value":"required",
                "end_date_value":"required",
            }
        });
    </script>
@endsection
