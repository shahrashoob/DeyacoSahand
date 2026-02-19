@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن انبار جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("wh.warehouse.store")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        @include("warehouse.warehouse._info")

                        <a href="{{route("wh.warehouse.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت انبار جدید</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")
    @include("component.input.select2._script")
    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "code": "required",
            }
        });
    </script>
@endsection
