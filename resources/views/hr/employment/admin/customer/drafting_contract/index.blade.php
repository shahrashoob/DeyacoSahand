@extends('layouts.admin._master')
@section('page_header_title',"کارتابل همکاری با ما/تنظیم پیشنویس قراداد هوشمند مشتری ")
@section("content")
    @include('component.input.datepicker.jalali_datepicker._style')
    <form id="form1"
          action="{{route("hr.employment.admin.customer.drafting_contract.submit",$employment)}}"
          method="post"
          novalidate="novalidate" autocomplete="off" enctype="multipart/form-data">
        @csrf
        <div class="row">

            <div class="col-md-6">

                <div class="card ">
                    <div class="card-header">
                        <h5>اطلاعات مالی مشتریان</h5>
                    </div>
                    <div class="card-block overflow-auto">

                        @include('hr.employment.admin.customer.drafting_contract._financial_info')
                        <div class="row">

                        </div>
                    </div>
                </div>


            </div>

            <div class="col-md-6">

                <div class="card">
                    <div class="card-header">
                        <h5>تاییدیه های مورد نیاز مشتری </h5>
                    </div>
                    <div class="card-block">

                        @include('hr.employment.admin.customer.drafting_contract._order_permission_customer')
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5> روش پرداخت فاکتورها</h5>
                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include('hr.employment.admin.customer.drafting_contract._payment_method_type')
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5> تنظیمات فرم ورود (تحویل امانی)</h5>
                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include('hr.employment.admin.customer.drafting_contract._input_form')

                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5> تنظیمات برگ خروج از انبار</h5>
                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include('hr.employment.admin.customer.drafting_contract._exit_form')

                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-12" style="text-align: center">
                <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}"
                   class="btn btn-outline-dark btn-lg">بازگشت</a>

                <button type="submit" class="btn btn-primary btn-lg"> ذخیره تغییرات</button>
            </div>
        </div>

    </form>

@endsection

@section("styles")

    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    @include('component.input.datepicker.jalali_datepicker._script')
    @include("component.input._seperated_number_3")
    <script>

        $('#form1').validate({
            rules: {
                detailed_code: "required",
                economic_number2: "required",
                code: "required",
                cash_off_percent: "required",
                bail_amount: "required",

                confirm_password: {equalTo: "#password"},
                tariff_id_auto: "required",
                priority_id_auto: "required",

                the_max_day_allowed_to_conform_exit_form_to: {required: true, min: 1},

                percent_tax_off_in_formal_factor: {required: true, min: 0, max: 100},
                percent_max_informal_purchase: {required: true, min: 0, max: 100},
                address: "required",
                country: "required",
                increase_percentage_in_informal_sale: "required",
                channel_id_auto: "required",
                financial_operation_pattern_id: "required",
                order_type_id_auto: "required",
                end_date_of_contract_value: "required",

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
    </script>
@endsection