@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت مشتریان")
@section("content")
    @include('component.input.datepicker.jalali_datepicker._style')
    <form id="form1" style="display: inline" action="{{route("customer_group.definition.admin.store")}}" method="post"
          novalidate="novalidate" autocomplete="off" enctype="multipart/form-data">
        @csrf
        <div class="row">

            <div class="col-md-6">

                <div class="card">
                    <div class="card-header">
                        <h5> افزودن مشتری جدید </h5>
                    </div>
                    <div class="card-block">

                        @include("customer.definition.admin._edit_customer_info")
                    </div>
{{--                    <div class="col-md-12" style="text-align: center">--}}
{{--                        <a href="{{route("customer_group.definition.admin.index")}}"--}}
{{--                           class="btn btn-outline-dark btn-lg">بازگشت</a>--}}

{{--                        <button type="submit" class="btn btn-primary btn-lg"> ذخیره</button>--}}
{{--                        <br/>--}}
{{--                    </div>--}}
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5> اطلاعات سامانه جامع </h5>
                    </div>
                    <div class="card-block">

                        @include("customer.definition.admin._software_system")
                    </div>
                    <div class="col-md-12" style="text-align: center">
                        <a href="{{route("customer_group.definition.admin.index")}}"
                           class="btn btn-outline-dark btn-lg">بازگشت</a>

                        <button type="submit" class="btn btn-primary btn-lg"> ذخیره</button>
                        <br/>
                    </div>
                </div>

            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>تاییدیه های مورد نیاز مشتری </h5>
                    </div>
                    <div class="card-block">

                        @include("customer.definition.admin._order_permission_customer")
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5> روش پرداخت فاکتورها</h5>
                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include("customer.definition.admin._payment_method_type_create")
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5> تنظیمات برگ خروج از انبار</h5>
                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include("customer.definition.admin._exit_form_create")
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>تنظیمات فرم ورود (تحویل امانی)</h5>
                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include("customer.definition.admin._input_form_create")
                        </div>
                    </div>
                </div>
{{--                <div class="card">--}}
{{--                    <div class="card-header">--}}
{{--                        <h5>اطلاعات آدرس و تماس مشتری</h5>--}}
{{--                    </div>--}}
{{--                    <div class="card-block overflow-auto">--}}


{{--                        <div class="row">--}}
{{--                            @include("customer.definition.admin._address_item_input")--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

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
    @include("component.input._seperated_number_3")
    <script>

        $('#form1').validate({
            rules: {
                customer_type_id: "required",
                user_id: "required",
                company_id: "required",
                email: "required",
                birth_date1_value: "required",
                birth_date2_value: "required",
                detailed_code: "required",
                province_id_auto: "required",
                channel_id_auto: "required",
                order_type_id_auto: "required",
                register_code2: "required",
                economic_number2: "required",
                code: "required",
                cash_off_percent: "required",
                bail_amount: "required",
                username: {
                    required: true,
                },
                national_code1: {
                    required: true,
                    number: true,
                    minlength: 10, maxlength: 10
                },
                national_code2: {
                    required: true,
                    number: true,
                    minlength: 11, maxlength: 11
                },
                password: {required: true, minlength: 8, maxlength: 11},
                confirm_password: {equalTo: "#password"},
                tariff_id_auto: "required",
                priority_id_auto: "required",

                mobile: {required: true, minlength: 10, maxlength: 10},
                phone: {required: true, minlength: 11, maxlength: 11},
                the_max_day_allowed_to_conform_exit_form_to: {required: true, min: 1},

                percent_tax_off_in_formal_factor: {required: true, min: 0, max: 100},
                percent_max_informal_purchase: {required: true, min: 0, max: 100},
                address: "required",
                country: "required",
                financial_operation_pattern_id: "required",
                increase_percentage_in_informal_sale: "required",
                software_system_id: "required",
                api_url: "required",
                api_username: "required",
                api_password: "required",
                api_key: "required",
            }
        });


        $(".payment_method").change(function () {

            if ($("#payment_method_type_" + $(this).data('id')).is(':checked')) {

                $(".payment_method_input_" + $(this).data('id')).css('display', "block");
            } else {

                $(".payment_method_input_" + $(this).data('id')).css('display', "none");
            }
        })
        $(".payment_method").change();

        setInterval(function () {
        }, 200)

        $("#customer_type_id").change(function () {

            if ($("#customer_type_id").val() == 1) {

                $("#customer_type1").css("display", "block");
                $("#customer_type2").css("display", "none");
            } else {
                $("#customer_type1").css("display", "none");
                $("#customer_type2").css("display", "block");
            }
        })
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
    </script>
@endsection
