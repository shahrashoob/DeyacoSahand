@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ثبت درخواست خروج متفرقه </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("wh.out.product_request_form_implementation.submit")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        <div class="col-md-3">
                            @include("component.input._aotocomplet2",[
                               "id"=>"warehouse_id",
                               "label"=>" انبار   ",
                               "option"=>$warehouse_option["items"],
                               "class_col"=>""
                           ])
                        </div>

                        <div class="col-md-3">
                            @include("component.input._select",[
                            "id"=>"trans_kind_id",
                            "label"=>" نوع تراکنش  ",
                            "option"=>$trans_kind_option["items"],
                            "val"=>$trans_kind_option["value"],
                            "text"=>$trans_kind_option["text"],
                            "class_col"=>""
                            ])
                        </div>
                        <div class="col-md-3">
                            @include("component.input._select",[
                            "id"=>"cost_center_id",
                            "label"=>" مرکز هزینه ",
                            "option"=>$cost_center_option["items"],
                            "val"=>$cost_center_option["value"],
                            "text"=>$cost_center_option["text"],
                            "class_col"=>""
                            ])
                        </div>
                        <div class="col-md-9" data-select2-id="119">

                            @include("component.input.select2._select2",[
                           "id"=>"product_ids",
                           "label"=>" لیست کالا ها جهت ثبت درخواست خروج از انبار ",
                           "option"=>$product_option["items"],
                           "class_col"=>""
                           ])

                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>
                        </div>


                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")


    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

    <style>
        li {
            direction: rtl !important;
        }
    </style>
    @include("component.input.select2._script")
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "warehouse_id_auto": "required",
                "product_ids": "required",
            }
        });
    </script>
@endsection
