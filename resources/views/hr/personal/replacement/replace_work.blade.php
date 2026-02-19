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

                    <form id="form1" action="{{route("hr.personal.replacement.submit_replace_work")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        <div class="row">
                            @include("component.input._hidden",["id"=>"start_datetime","value"=>$replacement_request["start_datetime"]])
                            @include("component.input._hidden",["id"=>"end_datetime","value"=>$replacement_request["end_datetime"]])

                            @include("component.input._hidden",["id"=>"start_datetime_return","value"=>$replacement_request["start_datetime_return"]])
                            @include("component.input._hidden",["id"=>"end_datetime_return","value"=>$replacement_request["end_datetime_return"]])

                            @include("component.input._hidden",["id"=>"text","lable"=>" توضیحات ","value"=>$replacement_request["text"]])


                            <div class="alert alert-info col-md-12">
                                لطفا همکار خود را انتخاب نمایید.
                            </div>

                            <div class="w-100"></div>


                        </div>

                        <div class="w-100"></div>
                        <div class="col-md-2">
                            @include("component.input._aotocomplet2",[
                                "id"=>"replace_user_id",
                                "label"=>"انتخاب همکار",
                                "option"=>$option_worker,
                                "val"=>0,
                                "text"=>"",
                                "class_col"=>""
                                ])
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
    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "replace_user_id_auto": "required",
                "end_datetime_value_return": "required",
                "end_time_h_return": "required",
                "start_datetime_value_return": "required",
                "start_time_h_return": "required",
            }
        });

    </script>
@endsection
