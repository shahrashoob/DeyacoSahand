@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>مدیریت عملیات
                        <b>
                            {{ $station_operation->caption}}
                        </b>
                        از ایستگاه
                        <b>
                            {{$station->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">

                    <form id="form" action="{{route("line_product_station.station.operation.update",$station_operation)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان عملیات  ","value"=>$station_operation->caption])
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"station_operation_type_id",
                                    "label"=>"  نوع عملیات  ",
                                    "option"=>$station_operation_type_option["items"],
                                    "val"=>$station_operation->station_operation_type->id??"",
                                    "text"=>$station_operation->station_operation_type->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"station_operation_category_id",
                                    "label"=>"  دسته عملیات  ",
                                    "option"=>$station_operation_category_option["items"],
                                    "val"=>$station_operation_category_option["value"],
                                    "text"=>$station_operation_category_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"discharge_type_id",
                                    "label"=>"  نوع حرکت مواد در ماشین ",
                                    "option"=>$discharge_type_option["items"],
                                    "val"=>$discharge_type_option["value"],
                                    "text"=>$discharge_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>


                        </div>



                        <button type="submit" class="btn btn-primary"> ویرایش</button>
                        <a href="{{route("line_product_station.station.operation.index",$station)}}"
                           class="btn btn-outline-dark">بازگشت</a>
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
        $('#form').validate({
            rules: {
                "caption": "required",
                "station_operation_type_id_auto": "required",
                "station_operation_category_id_auto": "required",
                "discharge_type_id_auto": "required",
            }
        });
    </script>
@endsection
