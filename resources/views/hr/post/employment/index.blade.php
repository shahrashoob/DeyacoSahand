@extends('layouts.admin._master')
@section("page_header_title","کارتابل منابع انسانی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات جذب برای
                        {{$post->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    @include("hr.post.employment._setting")



                </div>

            </div>


        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست گزینش ها
                    </h5>
                </div>
                <div class="card-block">


                    @include("hr.post.employment._list")


                </div>

            </div>


        </div>
        @endsection
        @section("styles")
            <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
            <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
        @endsection
        @section("scripts")
            <script>
                $('#form1').validate({
                    rules: {
                        "x": "required",

                    }
                });
            </script>
<script>
    $("#does_it_have_shift_work").change(function () {
        shift_id();
    })
    shift_id();

    function shift_id() {
        if ($("#does_it_have_shift_work").is(":checked")) {

            $("#shift_id").parent().css("display", "")
            $("#shift_delivery_module_id").parent().css("display", "")
        } else {
            $("#shift_id").parent().css("display", "none")
            $("#shift_delivery_module_id").parent().css("display", "none")
        }
    }
</script>

            <script>

                $(document).ready(function () {
                    // چک کردن وضعیت پیش‌فرض
                    if ($('#is_basis_for_daily_salary_on_labor_low_1').is(':checked')) {
                        $('#daily_salary').hide();
                        $('#daily_salary_label').hide();
                    } else {
                        $('#daily_salary').show();
                        $('#daily_salary_label').show();
                    }

                    // تغییر وضعیت بر اساس انتخاب
                    $('#is_basis_for_daily_salary_on_labor_low_0').change(function () {
                        if ($(this).is(':checked')) {
                            $('#daily_salary').show();
                            $('#daily_salary_label').show();
                        }
                    });

                    $('#is_basis_for_daily_salary_on_labor_low_1').change(function () {
                        if ($(this).is(':checked')) {
                            $('#daily_salary').hide();
                            $('#daily_salary_label').hide();
                        }
                    });
                });
            </script>

            <script>
                $(document).ready(function () {

                    if ($('#is_basis_for_duties_on_the_opinion_of_employer_1').is(':checked')) {
                        $('#duties').hide();
                        $('#duties_label').hide();
                    } else {
                        $('#duties').show();
                        $('#duties_label').show();
                    }

                    $('#is_basis_for_duties_on_the_opinion_of_employer_0').change(function () {
                        if ($(this).is(':checked')) {
                            $('#duties').show();
                            $('#duties_label').show();
                        }
                    });

                    $('#is_basis_for_duties_on_the_opinion_of_employer_1').change(function () {
                        if ($(this).is(':checked')) {
                            $('#duties').hide();
                            $('#duties_label').hide();
                        }
                    });
                });
            </script>
@endsection