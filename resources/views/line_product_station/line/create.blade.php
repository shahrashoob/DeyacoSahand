@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن خط تولید جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.line.store")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان خط ","value"=>$line->caption])


                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"ic",
                                    "label"=>" مرکز هزینه   ",
                                    "option"=>$cost_center_option["items"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"active_status_id",
                                    "label"=>" وضعیت فعال بودن  ",
                                    "option"=>$status_option["items"],
                                    "val"=>$line->status->id??"",
                                    "text"=>$line->status->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>

                        </div>

                        <a href="{{route("line_product_station.line.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت خط جدید</button>

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
                "code": "required",
                "status_id_auto": "required",
                "line_id_auto": "required",
                "ic_auto": "required",
                "warehouse_handling_time_limit": {required: true, min: 1}
            }
        });
    </script>
@endsection
