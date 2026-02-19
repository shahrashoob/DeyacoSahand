@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن مشتری جدید</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("oil_change.admin.store")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        @include("oil_change.admin._customer_info")

                        <a href="{{route("oil_change.admin.index")}}"
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
                firstname: "required",
                lastname: "required",
                shop_name: "required",
                email: "required",
                username: {
                    required: true,
                },
                national_code: {
                    required: true,
                    number: true,
                },
                password: {minlength: 8, maxlength: 11},
                mobile: {required:true,minlength: 11, maxlength: 11},
                confirm_password: {equalTo: "#password"},
            }
        });

    </script>
@endsection
