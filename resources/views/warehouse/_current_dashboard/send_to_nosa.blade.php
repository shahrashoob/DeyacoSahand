@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","انتقال اطلاعات به نوسا ")
@section("content")



    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> دریافت خروجی XML برای ارسال به نوسا </h5>
                </div>
                <div class="card-block">
                    <form id="form1" autocomplete="off" action="{{route("wh.send_to_nosa_xml")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"warehouse_id",
                                    "label"=>" انتخاب انبار ",
                                    "option"=>$warehouse_option["items"],
                                    "val"=>$warehouse_option["value"],
                                    "text"=>$warehouse_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            @include("component.input.datepicker._datepicker",["id"=>"start_date","lable"=>"از تاریخ "])

                            @include("component.input.datepicker._datepicker",["id"=>"end_date","lable"=>" تا تاریخ "])


                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"input_output_type",
                                    "label"=>" گروه تراکنش ",
                                    "option"=>[[ "id" => "5", "text" => "هر دو", "value" => "2" ],[ "id" => "1", "text" => "ورودی", "value" =>"1" ],[ "id" => "2", "text" => "خروجی", "value" => "-1" ], ],
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"group_by_ic","lable"=>"به تفکیک آیتم بسته بندی ","checked"=>true])
                            </div>
                        </div>

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary"> دریافت خروجی XML</button>
                        <input type="hidden" id="leading_false" value="1">
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
                "start_date_value": "required",
                "end_date_value": "required",
                "warehouse_id_auto": "required",
                "input_output_type": "required"
            }
        });
    </script>
@endsection
