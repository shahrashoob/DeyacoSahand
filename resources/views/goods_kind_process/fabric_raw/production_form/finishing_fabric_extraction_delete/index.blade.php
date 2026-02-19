@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تولید بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> استخراج فرم {{$production_form->getCode()}} </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.production_form.finishing_fabric_extraction.submit",$production_form)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        @include("goods_kind_process.fabric_raw.public._shift_and_counter")


                        <div class="w-100"></div>
                        <div class="col-md-6">
                            @include("component.input._aotocomplet2",[
                                "id"=>"fabric_raw_type_of_cut_id",
                                "label"=>" نوع برش پارچه ",
                                "option"=>$type_of_cut_option["items"],
                                "val"=>"",
                                "text"=>"",
                                "class_col"=>""
                                ])
                        </div>

                        @if($production_form->status_id == 7002008)
                            @include("component.input._text",["id"=>"carrier_id","label"=>"شماره غلطک جدید پارچه خام"])
                        @endif


                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.production_form.dashboard.view",$production_form)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">تایید و ادامه</button>
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
@endsection
@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "shift_work_id_auto": "required",
                "contour_1_value": "required",
                "contour_2_value": "required",
                "contour_3_value": "required",

                "fabric_raw_type_of_cut_id_auto": "required",
                "carrier_id": "required",
            }
        });
    </script>
@endsection
