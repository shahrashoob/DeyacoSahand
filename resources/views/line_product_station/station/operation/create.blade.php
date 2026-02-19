@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن عملیات به ایستگاه کاری {{$station->caption}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.station.operation.store",$station)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان عملیات  ","value"=>""])

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"station_operation_type_id",
                                    "label"=>"  نوع عملیات  ",
                                    "option"=>$station_operation_type_option["items"],
                                    "val"=>"",
                                    "text"=>"",
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

                        <a href="{{route("line_product_station.station.index",$station->line_id)}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> افزودن</button>

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
                "caption": "required",
                "station_operation_type_id_auto": "required",
                "station_operation_category_id_auto": "required",
                "discharge_type_id_auto": "required",
            }
        });
    </script>
@endsection
