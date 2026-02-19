@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> لیست انبارک های  خط {{$line->caption}} </h5>
                </div>
                <div class="card-block">

                    @include("warehouse.warehouse._info_list",["warehouses"=>$line->warehouses])


                    <div class="center">
                        <a href="{{route("line_product_station.line.index")}}" class="btn btn-outline-dark">بازگشت</a>
                    </div>
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
                "status_id_auto": "required",
            }
        });
    </script>
@endsection
