@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <form id="form1" action="{{route("line_product_station.packing.packing_type.store")}}" method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> افزودن نوع بسته بندی جدید </h5>
                    </div>
                    <div class="card-block">


                        <div class="row">
                            @include("line_product_station.packing.packing_type._info")
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"carrier_type_id",
                                    "label"=>" نوع حامل (اولین لایه)   ",
                                    "option"=>$carrier_option["items"],
                                    "class_col"=>""
                                    ])
                            </div>


                        </div>
                    </div>
                </div>

            </div>
            @include("line_product_station.packing.packing_type._goods_kind")
            <div class="col-md-12">
                <a href="{{route("line_product_station.packing.packing_type.index")}}"
                   class="btn btn-outline-dark">بازگشت</a>

                <button type="submit" class="btn btn-primary"> ثبت</button>
            </div>
        </div>
    </form>

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
                "active_status_id_auto": "required",
                "carrier_type_id_auto": "required",
                "packing_type_label_printing_type_id_auto": "required",
                "label_caption": "required",
                "weight": "required",
                "length": "required",
                "width": "required",
                "height": "required",
                "weight_error_percentage": "required",
                "discharge_type_id_auto": "required",
            }

        });
    </script>
@endsection
