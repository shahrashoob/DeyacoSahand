@extends('layouts.admin._master')
@section("page_header_title","داشبورد جاری سفارشات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>صدور دستور جمع آوری بار برای سفارش {{$order->code()}}</h5>
                </div>
                <div class="card-block">


                    <form id="form1" action="{{route("sales.loading.dashboard.order_to_collection_submit",$order)}}" method="post"
                          novalidate="novalidate" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"supervisor_collect_post_id",
                                    "label"=>" تیم جمع آوری بار ",
                                    "option"=>$post_list["items"],
                                    "val"=>$post_list["value"],
                                    "text"=>$post_list["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"supervisor_loading_post_id",
                                    "label"=>" تیم بارگیری",
                                    "option"=>$post_list["items"],
                                    "val"=>$post_list["value"],
                                    "text"=>$post_list["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"car_type_id",
                                    "label"=>" نوع وسیله نقلیه",
                                    "option"=>$car_types_option["items"],
                                    "val"=>$car_types_option["value"],
                                    "text"=>$car_types_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"car_type_id",
                                    "label"=>" درب ورودی",
                                    "option"=>$doors_option["items"],
                                    "val"=>$doors_option["value"],
                                    "text"=>$doors_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>



                        </div>

                        <a href="{{route("accounting.offer.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت تخفیف جدید</button>

                    </form>

                </div>
                <div class="text-center">

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
                "supervisor_loading_post_id_auto": "required",
                "supervisor_collect_post_id_auto": "required",
                "car_type_id_auto": "required"
            }
        });
    </script>
@endsection

