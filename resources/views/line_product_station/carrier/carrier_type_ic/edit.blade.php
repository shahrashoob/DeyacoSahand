@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت/بروز رسانی اطلاعات حامل ")

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <form id="form1"
                  action="{{route("line_product_station.carrier.carrier_type_ic.update",$carrier_type)}}"
                  method="post"
                  novalidate="novalidate">
                @csrf
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-header">
                                <h5> اطلاعات نوع حامل در منظومه داده ای </h5>
                            </div>
                            <div class="card-block">
                                @include('line_product_station.carrier.carrier_type_ic._list_in_ic')
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>
                                    اطلاعات نوع حامل در
                                    {{$software_name}}
                                </h5>
                            </div>
                            <div class="card-block">
                                @include('line_product_station.carrier.carrier_type_ic._list')

                            </div>
                        </div>
                    </div>
                </div>


                <div class="center">
                    <a href="{{route("line_product_station.carrier.carrier_type.index")}}"
                       class="btn btn-outline-dark ">بازگشت</a>

                    <button type="submit" class="btn btn-primary"> بروزرسانی</button>
                </div>
            </form>
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
                "length": "required",
                "width": "required",
                "height": "required",
                "weight": "required",
            }
        });
    </script>
@endsection
