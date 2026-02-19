@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","کارتابل برنامه ریزی ")
@section("content")



    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5> برگ دستور تولید</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("utility.planing.production_order_submit")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"product_id",
                                    "label"=>" انتخاب کالا ",
                                    "option"=>$product_option["items"],
                                    "val"=>$product_option["value"],
                                    "text"=>$product_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"warehouse_id",
                                    "label"=>" انتخاب انبار کالا ",
                                    "option"=>$warehouse_option["items"],
                                    "val"=>$warehouse_option["value"],
                                    "text"=>$warehouse_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"priority_id",
                                    "label"=>" اولویت ",
                                    "option"=>$priority_option["items"],
                                    "val"=>$priority_option["value"],
                                    "text"=>$priority_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            @include("component.input._number",["id"=>"carton","lable"=>"تعداد  ( واحد اصلی) "])

                        </div>

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary">صدور کارت تولید</button>

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
                "carton": "required",
                "warehouse_id_auto": "required",
                "product_id_auto": "required",
                "priority_id_auto": "required",
            }
        });
    </script>
@endsection
