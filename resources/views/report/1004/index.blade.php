@extends('layouts.admin._master')
@section("page_header_title","گزارش 1004 -  کاردکس کالا")

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>گزارش 1004 -   کاردکس کالا</h5>
                </div>

                <div class="card-block">
                    <form id="form1" autocomplete="off" action="{{route("report.1004.submit_form")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            @include("component.input.datepicker._datepicker",["id"=>"start_date","lable"=>"از تاریخ "])

                            @include("component.input.datepicker._datepicker",["id"=>"end_date","lable"=>" تا تاریخ "])

                            <div class="w-100"></div>
                            <div class="col-md-3">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"product_id",
                                    "label"=>" کالا  ",
                                    "option"=>$product_option["items"],
                                    "val"=>$product_option["value"],
                                    "text"=>$product_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-3">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"from_warehouse_id",
                                    "label"=>" انبار از ",
                                    "option"=>$warehouse_option["items"],
                                    "val"=>$warehouse_option["value"],
                                    "text"=>$warehouse_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="col-md-3">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"to_warehouse_id",
                                    "label"=>"  انبار تا ",
                                    "option"=>$warehouse_option["items"],
                                    "val"=>$warehouse_option["value"],
                                    "text"=>$warehouse_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                        </div>

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary"> دریافت فایل اکسل </button>

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
                "product_id_auto":"required",
            }
        });
    </script>
@endsection
