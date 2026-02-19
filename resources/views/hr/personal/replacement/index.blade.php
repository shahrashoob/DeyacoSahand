@extends('layouts.admin._master')

@section("page_header_title","کارتابل  منابع انسانی ")


@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ثبت درخواست جابجایی شیفت </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.personal.replacement.submit")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            <div class="col-md-12"><h5>اینجانب درخواست جابجایی حضور خود </h5></div>

                            @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"start_datetime","hasTime"=>1,"lable"=>" از تاریخ و ساعت ","class_col"=>"col-md-2","value"=>$replacement_request["start_datetime"]])

                            @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"end_datetime","hasTime"=>1,"lable"=>" تا تاریخ و ساعت ","class_col"=>"col-md-2","value"=>$replacement_request["end_datetime"]])


                        </div>
                        <div class="row">
                            <div class="col-md-12"><h5>با همکارم </h5></div>


                            @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"start_datetime_return","hasTime"=>1,"lable"=>" از ساعت و تاریخ ","class_col"=>"col-md-2","value"=>$replacement_request["start_datetime_return"]])


                            @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"end_datetime_return","hasTime"=>1,"lable"=>" تا ساعت و تاریخ ","class_col"=>"col-md-2","value"=>$replacement_request["end_datetime_return"]])


                            <div class="col-md-12"><h5>دارم. </h5></div>
                            <br/>
                            <br/>
                            <div class="w-100"></div>
                            @include("component.input._textarea",["id"=>"text","lable"=>" توضیحات ","class_col"=>"col-md-2","value"=>$replacement_request["text"]])

                        </div>

                        <a href="{{route("hr.personal.index",[$worker,$worker->random])}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت</button>

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
                "text": "required",

                "end_datetime_value_return": "required",
                "end_time_h_return": "required",
                "start_datetime_value_return": "required",
                "start_time_h_return": "required",
            }
        });
    </script>
@endsection
