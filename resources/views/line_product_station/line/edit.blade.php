@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش خط {{$line->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.line.update",$line)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"code",'label'=>"کد خط","value"=>$line->code,"readonly"=>1])
                            @include("component.input._text",["id"=>"caption",'label'=>"نام خط ","value"=>$line->caption,"autofocus"=>1])

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"ic",
                                    "label"=>" مرکز هزینه   ",
                                    "option"=>$cost_center_option["items"],
                                    "val"=>$cost_center_option["value"],
                                    "text"=>$cost_center_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"active_status_id",
                                    "label"=>" وضعیت فعال بودن  ",
                                    "option"=>$status_option["items"],
                                    "val"=>$line->active_status->id??"",
                                    "text"=>$line->active_status->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>

                        </div>

                        <a href="{{route("line_product_station.line.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                    </form>

                </div>
            </div>
        </div>


        @include("line_product_station.machine_type.warehouse_handling._goods_kind_warehouse_handling",[
          "warehouse_type_id"=>5,
          "belonging_to_id"=>$line->id,
          "url"=>route("line_product_station.line.property_update",$line
          )])
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
                "status_id_auto": "required",
                "ic_auto": "required",
                "warehouse_handling_time_limit": {required: true, min: 1}
            }
        });
    </script>
@endsection
