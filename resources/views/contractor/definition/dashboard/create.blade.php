@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت پیمانکاران")
@section("content")
    @include('component.input.datepicker.jalali_datepicker._style')
    <form id="form1" style="display: inline" action="{{route("contractor.definition.dashboard.store")}}" method="post"
          novalidate="novalidate" autocomplete="off" enctype="multipart/form-data">
        @csrf
        <div class="row">

            <div class="col-md-6">

                <div class="card">
                    <div class="card-header">
                        <h5> افزودن پیمانکار جدید </h5>
                    </div>
                    <div class="card-block">

                        @include("contractor.definition.dashboard._edit_contractor_info")
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5> اطلاعات سامانه جامع </h5>
                    </div>
                    <div class="card-block">

                        @include("contractor.definition.dashboard._software_system")
                    </div>
                    <div class="col-md-12" style="text-align: center">
                        <a href="{{route("contractor.definition.dashboard.index")}}"
                           class="btn btn-outline-dark btn-lg">بازگشت</a>

                        <button type="submit" class="btn btn-primary btn-lg"> ذخیره</button>
                        <br/>
                    </div>
                </div>

            </div>
            <div class="col-md-6">
{{--                <div class="card">--}}
{{--                    <div class="card-header">--}}
{{--                        <h5>اطلاعات آدرس و تماس پیمانکار</h5>--}}
{{--                    </div>--}}
{{--                    <div class="card-block overflow-auto">--}}


{{--                        <div class="row">--}}
{{--                            @include("contractor.definition.dashboard._address_item_input")--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="card">
                    <div class="card-header">
                        <h5>تنظیمات برگ خروج</h5>
                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include("contractor.definition.dashboard._exit_form")
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>تنظیمات ثبت اطلاعات تولید</h5>
                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include("contractor.definition.dashboard._input_form")
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </form>
    @include('component.input.datepicker.jalali_datepicker._script')
@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")

    <script>

        $('#form1').validate({
            rules: {
                caption: "required",
                user_id:"required",
                comapny_id:"required",
                personal_type_id:"required",
                firstname: "required",
                lastname: "required",
                email: "required",
                post_id_auto: "required",
                province_id_auto: "required",
                cost_center_id_auto: "required",
                username: {
                    required: true,
                },
                national_code: {
                    required: true,
                    number: true,
                },
                password: {required: true, minlength: 8, maxlength: 11},
                confirm_password: {equalTo: "#password"},


                mobile: {required: true, minlength: 10, maxlength: 10},
                address: "required",
                country: "required",
                active_status_id_auto: "required",
                minimum_time_required_to_start_coordination: "required",
                start_of_work_time_h: "required",
                end_of_work_time_h: "required",
                software_system_id: "required",
                api_url: "required",
                api_username: "required",
                api_password: "required",
                api_key: "required",
                start_date_of_contract_value: "required",
                end_date_of_contract_value: "required",

            }
        });

        setInterval(function () {
        }, 200)

        $("#software_system_id").change(function () {
            software_system()
        });
        software_system()
        function software_system(){
            if ($("#software_system_id").val()!=0) {
                $(".software").show();
            } else {
                $(".software").hide();
            }
        }
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
