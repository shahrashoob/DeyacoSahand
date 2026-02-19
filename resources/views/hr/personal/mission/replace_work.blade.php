@extends('layouts.admin._master')

@section("page_header_title","کارتابل  منابع انسانی ")


@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ثبت درخواست  ماموریت
                <div class="card-block">

                    <form id="form1" action="{{route("hr.personal.mission.submit_replace_work")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">


                            @include("component.input._hidden",["id"=>"start_datetime","value"=>$mission_request["start_datetime"]])
                            @include("component.input._hidden",["id"=>"end_datetime","value"=>$mission_request["end_datetime"]])


                            @include("component.input._hidden",["id"=>"text","lable"=>" توضیحات ","value"=>$mission_request["text"]])


                            <div class="alert alert-info col-md-12">
                                با توجه به اینکه در زمان ماموریت نیاز است، تا برای خود جانشین انتخاب کنید، لطفا به ازای هر
                                پست یک جانشین انتخاب نمایید.
                            </div>

                            @foreach($option_workers as $option_worker)
                                <div class="w-100"></div>
                                <div class="col-md-3">
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

                        <button type="submit" class="btn btn-primary"> ثبت ماموریت جدید</button>

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
