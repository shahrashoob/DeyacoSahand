@extends('layouts.admin._master')
@section("page_header_title"," تولید / داشبورد مقطعی مدیریت")

@section("content")
    <div class="row">

        <div class="col-sm-12">


            <div class="card">
                <div class="card-header">
                    <h5> داشبورد مقطعی مدیریت</h5>
                </div>

                <div class="card-block">
                    <form id="form1" autocomplete="off"
                          action="{{route("report.1013.submit_download_excel")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">

                                    <div class="col-md-10">
                                        @include("component.input._select",[
                                            "id"=>"customer_id",
                                            "label"=>"مشتری  ",
                                            "option"=>$customer_option["items"],
                                            "val"=>$customer_option["value"],
                                            "text"=>$customer_option["text"],
                                            "class_col"=>""
                                            ])
                                    </div>
                                    <div class="w-100"><br/></div>
                                    <div class="col-md-10">
                                        @include("component.input._select",[
                                            "id"=>"report_type_status_id",
                                            "label"=>"نوع گزارش ",
                                             "option"=>$report_type_status_option,
                                            "class_col"=>""
                                            ])
                                    </div>
                                    <div class="w-100"><br/></div>
                                    <div class="col-md-10">
                                        @include("component.input._select",[
                                            "id"=>"price_or_product",
                                            "label"=>"نوع مقداری",
                                             "option"=>[["value"=>"price","caption"=>"ریالی"],["value"=>"amount","caption"=>"مقداری"]],
                                            "class_col"=>""
                                            ])
                                    </div>

                                </div>
                            </div>

                        </div>

                        <br/>
                        <a href="{{route("report.1013.index")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary"> دریافت فایل گزارش</button>
                        <input type="hidden" id="leading_false" value="1">
                    </form>
                </div>

            </div>
        </div>

    </div>

@endsection
@section("styles")


    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection


@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "price_or_product": "required",
                "report_type_status_id": "required",
                "customer_id": "required",
            }
        });
    </script>
@endsection
