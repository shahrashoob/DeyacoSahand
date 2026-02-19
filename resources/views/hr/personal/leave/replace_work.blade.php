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

                    <form id="form1" action="{{route("hr.personal.leave.submit_replace_work")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._hidden",["id"=>"leave_overtime_type_id","value"=>$leave_overtime_request["leave_overtime_type_id"]])
                            @include("component.input._hidden",["id"=>"start_datetime","value"=>$leave_overtime_request["start_datetime"]])
                            @include("component.input._hidden",["id"=>"end_datetime","value"=>$leave_overtime_request["end_datetime"]])


                            @include("component.input._hidden",["id"=>"text","lable"=>" توضیحات ","value"=>$leave_overtime_request["text"]])


                            <div class="alert alert-info col-md-12">
                                با توجه به اینکه در زمان مرخصی نیاز است، تا برای خود جانشین انتخاب کنید، لطفا به ازای هر
                                پست یک جانشین انتخاب نمایید.
                            </div>

                            @foreach($option_workers as $option_worker)
                                <div class="w-100"></div>
                                <div class="col-md-2">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"replace_user_id_".$option_worker["post"]->id,
                                        "label"=>"جانشین ".$option_worker["post"]->caption,
                                        "option"=>$option_worker["option"],
                                        "val"=>0,
                                        "text"=>"",
                                        "class_col"=>""
                                        ])
                                </div>
                            @endforeach

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

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                @foreach($option_workers as $option_worker)
                "replace_user_id_{{$option_worker["post"]->id}}_auto": "required",
                @endforeach

            }
        });
    </script>
@endsection
