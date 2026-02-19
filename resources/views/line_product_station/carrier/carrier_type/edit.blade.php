@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش نوع حامل  {{$carrier_type->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.carrier.carrier_type.update",$carrier_type)}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        @if($edit_main_property_carrier_type)
                            @include("line_product_station.carrier.carrier_type._info")
                            @else
                            @include("line_product_station.carrier.carrier_type._info_edit_sub_property")
                        @endif


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
