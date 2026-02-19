@extends('layouts.admin._master')

@section("page_header_title","کارتابل  منابع انسانی ")


@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ثبت درخواست مرخصی </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.personal.leave.submit")}}" method="post" autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">


                            <div class="col-md-3">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"leave_overtime_type_id",
                                    "label"=>"نوع مرخصی ",
                                    "option"=>$leave_type_option["items"],
                                    "val"=>$leave_type_option["value"],
                                    "text"=>$leave_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"start_datetime","hasTime"=>1,"lable"=>" از تاریخ و ساعت ","class_col"=>"col-md-3","value"=>$leave_overtime_request["start_datetime"]])
                            <div class="w-100"></div>
                            @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"end_datetime","hasTime"=>1,"lable"=>" تا تاریخ و ساعت ","class_col"=>"col-md-3","value"=>$leave_overtime_request["end_datetime"]])
<div class="w-100"></div>
                            @include("component.input._textarea",["id"=>"text","lable"=>" توضیحات ","class_col"=>"col-md-3","value"=>$leave_overtime_request["text"]])


                        </div>

                        <a href="{{route("hr.personal.index",[$worker,$worker->random])}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت مرخصی جدید</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker.jalali_datepicker._style")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    @include("component.input.datepicker.jalali_datepicker._script")
    <script>
        $('#form1').validate({
            rules: {
                "leave_overtime_type_id_auto": "required",
                "end_datetime_value": "required",
                "end_time_h": "required",
                "start_datetime_value": "required",
                "start_time_h": "required",
                "text": "required"
            }
        });
    </script>
@endsection
