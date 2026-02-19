@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن خط ورودی به گروه ماشین {{$machine_type->caption}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.machine_type.input_band.store",$machine_type)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._number",["id"=>"input_line_number",'label'=>"حداکثر تعداد کل خط ورودی  ","value"=>"1"])

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"goods_kind_id",
                                    "label"=>" رسته کالای ورودی  ",
                                    "option"=>$goods_kind_option["items"],
                                    "class_col"=>""
                                    ])
                            </div>


                            <div class="col-md-12">
                                <br/>

                                <div class="col-md-6">
                                    @include("component.input._checkbox",["id"=>"can_used_material_with_different_lot_per_production_card","checked"=>false,'label'=>"آبا برای هر کارت تولید امکان مصرف ماده با لات های مختلف وجود دارد؟"])

                                </div>

                            </div>

                        </div>

                        <a href="{{route("line_product_station.machine_type.index",$machine_type->station_id)}}"
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
                "goods_kind_id_auto": "required",
                "line_input_number": "required",

            }
        });
    </script>
@endsection
