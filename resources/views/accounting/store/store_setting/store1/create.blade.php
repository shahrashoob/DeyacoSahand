@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مالی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تنظیمات
                        {{$store->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("accounting.store.store_setting.store1.store",[$store,$special_license_type])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        @include('utility.special_license.definition.dashboard._info')

                        <a href="{{route("accounting.store.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

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
    @include("component.input.select2._script")
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
