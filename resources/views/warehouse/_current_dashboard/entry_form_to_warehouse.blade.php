@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار  ")

@section('content')

    <div class="row">
        <form id="form1" autocomplete="off" action="{{route("wh.entry_form_to_warehouse_submit")}}"
              method="post"
              novalidate="novalidate">
            @csrf

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>ثبت فرم ورود به انبار </h5>
                    </div>
                    <div class="card-block">


                        <div class="row">
                            <div class="col-md-4">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"factory_id",
                                    "label"=>" انتخاب کارخانه / شرکت ",
                                    "option"=>$factory_option["items"],
                                    "val"=>$factory_option["value"],
                                    "text"=>$factory_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-4">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"warehouse_id",
                                    "label"=>" انتخاب انبار ",
                                    "option"=>$warehouse_option["items"],
                                    "val"=>$warehouse_option["value"],
                                    "text"=>$warehouse_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-4">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"opp_kind_id",
                                    "label"=>" طرف حساب ",
                                    "option"=>$opp_kind_option["items"],
                                    "val"=>$opp_kind_option["value"],
                                    "text"=>$opp_kind_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-4">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"trans_kind_id",
                                    "label"=>" نوع تراکنش  ",
                                    "option"=>$trans_kind_option["items"],
                                    "val"=>$trans_kind_option["value"],
                                    "text"=>$trans_kind_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            @include("component.input._text",["label"=>"مرکز هزینه","id"=>"ic","class_col"=>"col-md-4"])

                            @include("component.input._textarea",["label"=>"شرح ","id"=>"description","width"=>"600px", "height"=>"100px"])
                        </div>
                        <div class="row">

                            @for($i= 1; $i < 2; $i++)
                                <div class="col-md-4">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"product_".$i,
                                        "label"=>"نام محصول",
                                        "option"=>$product_option["items"],
                                        "val"=>$product_option["value"],
                                        "text"=>$product_option["text"],
                                        "class_col"=>""
                                        ])
                                </div>
                                @include("component.input._text",["label"=>"مقدار ","id"=>"data[".$i."][carton]","class_col"=>"col-md-2"])
                                <div class="w-100"></div>


                            @endfor

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                <button type="submit" class="btn btn-primary"> ثبت فرم </button>

            </div>

        </form>
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
                "ic": "required",
               "trans_kind_id_auto":"required" ,
                "opp_kind_id_auto": "required",
                "warehouse_id_auto": "required",
                "product_1_auto": "required"
            }
        });
    </script>
@endsection

