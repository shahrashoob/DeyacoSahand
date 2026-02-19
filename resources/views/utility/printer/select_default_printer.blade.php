@extends('layouts.admin._master')

@section('page_header_title'," کارتابل شخصی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> انتخاب پرینتر پیش فرض </h5>
                    <div class="label float-right ">
                        <a class="btn btn-primary" href="{{url("upload/utility_files/DCPrinter.zip")}}">دانلود اسکریپت پرینتر</a>
                    </div>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.printer.submit_select_default_printer")}}" method="post" autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                                <div class="col-md-6">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"default_printer_id",
                                        "label"=>" پرینتر",
                                        "option"=>$printer_option["items"],
                                        "val"=>$printer_option["value"],
                                        "text"=>$printer_option["text"],
                                        "class_col"=>""
                                        ])
                                </div>
                            <div class="col-md-2">
                                @include("component.input._aotocomplet",[
                                    "id"=>"default_print_number",
                                    "label"=>"تعداد پرینت برگ خروج ",
                                    "text_white"=>1,
                                    "option"=>$print_number_option["items"],
                                    "val"=>$print_number_option["value"],
                                    "text"=>$print_number_option["text"],
                                    ])
                            </div>
                            <div class="w-100"></div>
                                <div class="col-md-6">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"default_label_printer_id",
                                        "label"=>" لیبل پرینتر",
                                        "option"=>$label_printer_option["items"],
                                        "val"=>$label_printer_option["value"],
                                        "text"=>$label_printer_option["text"],
                                        "class_col"=>""
                                        ])
                                </div>

                            <div class="col-md-2">
                                @include("component.input._aotocomplet",[
                                    "id"=>"default_label_print_number",
                                    "label"=>"تعداد پرینت برگ خروج ",
                                    "text_white"=>1,
                                    "option"=>$label_print_number_option["items"],
                                    "val"=>$label_print_number_option["value"],
                                    "text"=>$label_print_number_option["text"],
                                    ])
                            </div>

                        </div>

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت </button>

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
                "default_label_printer_id_auto": "required",
                "default_printer_id_auto": "required",
            }
        });
    </script>
@endsection
