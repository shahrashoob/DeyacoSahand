@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن نوع حامل   </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.carrier.carrier_type.store")}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        @include("line_product_station.carrier.carrier_type._info")

                        <a href="{{route("line_product_station.carrier.carrier_type.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary">  ذخیره تغییرات </button>

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
                "carrier_group_id_auto": "required",
                "unit_id_auto": "required",
                "min_band_number": "required",
                "max_band_number": "required",
                "min_band_capacity": "required",
                "max_band_capacity": "required",
                "length":"required",
                "width":"required",
                "height":"required",
                "weight":"required",
            }
        });
    </script>
@endsection
