@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن لایه جدید به {{$packing_type->fullCaption()}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.packing.packing_type.add_layer_store",$packing_type)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"first_packing_type_id",
                                    "label"=>" نوع بسته بندی لایه اول ",
                                    "option"=>$packing_type_options,
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"carrier_type_id",
                                    "label"=>" نوع حامل برای لایه بیرونی ",
                                    "option"=>$carrier_option["items"],
                                    "class_col"=>""
                                    ])
                            </div>


                        </div>

                        <a href="{{route("line_product_station.packing.packing_type.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت </button>

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
                "carrier_type_id_auto": "required",
                "first_packing_type_id_auto": "required",
            }
        });
    </script>
@endsection
