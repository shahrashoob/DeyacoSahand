@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن عملیات فرعی به عملیات {{$station_operation->caption}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.station.sub_operation.store",$station_operation)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان عملیات فرعی  ","value"=>""])

                        </div>

                        <a href="{{route("line_product_station.station.operation.index",$station_operation->station_id)}}"
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
                "caption": "required",
            }
        });
    </script>
@endsection
