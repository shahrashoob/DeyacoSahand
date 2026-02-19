@extends('layouts.admin._master')
@section("page_header_title","گزارش 1 - داشبورد پیمانکاران")

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>گزارش تولید پیمان کاری</h5>
                </div>

                <div class="card-block">
                    <form id="form1" autocomplete="off" action="{{route("contractor.report.report_1.submit")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            @include("component.input.datepicker._datepicker",["id"=>"start_date","lable"=>"از تاریخ ","class_col"=>"col-md-4"])
                            <div class="w-100"></div>
                            @include("component.input.datepicker._datepicker",["id"=>"end_date","lable"=>" تا تاریخ ","class_col"=>"col-md-4"])

                            <div class="w-100"></div>
                            <div class="col-md-4">
                                @include("component.input._select",[
                                    "id"=>"goods_kind_id",
                                    "label"=>"رسته کالا",
                                    "option"=>$goods_kind_option["items"],
                                    "val"=>$goods_kind_option["value"],
                                    "text"=>$goods_kind_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-4">
                                @include("component.input._select",[
                                    "id"=>"goods_kind_property_id",
                                    "label"=>"مشخصه کالا",
                                    "option"=>$property_option["items"],
                                    "val"=>$property_option["value"],
                                    "text"=>$property_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-4" id="search_input">
                                @include("component.input._text",[
                                "id"=>"search",
                                "label"=>" متن جستجو",
                                "value"=>"",
                                "class_col"=>""
                                ])
                            </div>

                            <div class="w-100"></div>

                                @include("component.input._text",[
                                "id"=>"order_search",
                                "label"=>"شماره سفارش",
                                "value"=>"",
                                "class_col"=>"col-md-4"
                                ])




                        </div>

                        @include("component.input._hidden",[
                                   "id"=>"report_type",
                                   "value"=>"excel",
                                   ])

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" id="download_excel" class="btn btn-primary"> دریافت فایل اکسل </button>
                        <button type="button" id="download_pdf" class="btn btn-primary"> چاپ گزارش بر اساس کد کالا </button>
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
    @include("component.script_function.get_new_option")
    <script>

        $("#download_pdf").click(function (){
           $("#report_type").val("pdf");
           $("#form1").submit();
        });
        $("#download_excel").click(function (){
           $("#report_type").val("excel");
        });

        $("#goods_kind_id").change(function () {
            get_new_option(
                $("#goods_kind_property_id").val(),
                $("#goods_kind_id").val(),
                "مشخصه کالایی",
                "goods_kind_property_id",
                "get_property_by_goods_kind",
            )
        })

        $("#goods_kind_property_id").change(function () {
            update_input_search();
        })

        function update_input_search() {
            request = $.ajax({
                url: "{{url("api/other/get_property_input_by_property_id")}}",
                type: "post",
                data: {
                    "id": $("#goods_kind_property_id").val(),
                    "value": $("#search").val()
                }
            });
            request.done(function (response, textStatus, jqXHR) {

                $("#search_input").html(response);
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });
        }

        update_input_search();


        $('#form1').validate({
            rules: {
                "start_date_value":"required",
                "end_date_value":"required",
            }
        });
    </script>
@endsection
