@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت تامین کنندگان")
@section("content")
    @include('component.input.datepicker.jalali_datepicker._style')
    <form id="form1" style="display: inline" action="{{route("supplier.definition.dashboard.update",$supplier)}}"
          method="post"
          novalidate="novalidate" autocomplete="off" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5> تامین کننده {{$supplier->code ." - ".$supplier->caption}}</h5>
                    </div>
                    <div class="card-block">

                        @include("supplier.definition.dashboard._edit_supplier_info")
                    </div>
                </div>
            </div>
            <div class="col-md-6">

{{--                <div class="card">--}}
{{--                    <div class="card-header">--}}
{{--                        <h5>اطلاعات آدرس و تماس تامین کننده</h5>--}}
{{--                    </div>--}}
{{--                    <div class="card-block overflow-auto">--}}


{{--                        <div class="row">--}}
{{--                            @include("supplier.definition.dashboard._address_item_input")--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}




                <div class="card">
                    <div class="card-header">
                        <h5>تنظیمات ثبت اطلاعات تامین</h5>
                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include("supplier.definition.dashboard._input_form")
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>تنظیمات برگ خروج (تحویل امانی)</h5>
                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include("supplier.definition.dashboard._exit_form")
                        </div>
                    </div>
                </div>

            </div>


            <div class="col-md-12" style="text-align: center">
                <a href="{{route("supplier.definition.dashboard.index")}}"
                   class="btn btn-outline-dark btn-lg">بازگشت</a>

                <button type="submit" class="btn btn-primary btn-lg"> ذخیره</button>
            </div>
        </div>

    </form>
    @include('component.input.datepicker.jalali_datepicker._script')
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
                caption: "required",
                firstname: "required",
                user_id: "required",
                personal_type_id:"required",

                company_id: "required",
                lastname: "required",
                email: "required",
                province_id_auto: "required",
                cost_center_id_auto: "required",
                detailed_code:"required",
                username: {
                    required: true,
                },
                national_code: {
                    required: true,
                    number: true,
                },
                password: {minlength: 8, maxlength: 11},
                confirm_password: {equalTo: "#password"},


                mobile: {required: true, minlength: 10, maxlength: 10},
                address: "required",
                country: "required",
                active_status_id_auto: "required",
                supplier_type_id_auto: "required",
            }
        });
        $("#personal_type_id").change(function () {

            if ($("#personal_type_id").val() == 1) {

                $("#personal_type1").css("display", "block");
                $("#personal_type2").css("display", "none");
            } else {
                $("#personal_type1").css("display", "none");
                $("#personal_type2").css("display", "block");
            }
        })
    </script>
@endsection
