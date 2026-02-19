@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن مخزن جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.reservoir.definition.update",$reservoir)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            @include("line_product_station..reservoir.definition._info")


                            <div class="col-md-12">
                                <a href="{{route("line_product_station.reservoir.definition.index")}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary">  ذخیره</button>
                            </div>
                        </div>
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
                "capacity": "required",
                "unit_id": "required",
                "warehouse_id": "required",
                "reservoir_type_id": "required",
                "active_status_id": "required",
            }
        });
    </script>
@endsection
