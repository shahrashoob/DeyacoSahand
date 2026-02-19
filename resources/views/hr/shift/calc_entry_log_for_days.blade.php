@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> محاسبه مجدد کارکرد پرسنل </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.shift.submit_calc_entry_log_for_days")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"start_date","lable"=>"از تاریخ ","class_col"=>"col-md-3",])

                            @include("component.input._number",["id"=>"days",'label'=>" تا چند روز قبل","value"=>"10","class_col"=>"col-md-3"])


                        </div>

                        <a href="{{route("hr.shift.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker.jalali_datepicker._style")
@endsection


@section("scripts")

    @include("component.input.datepicker.jalali_datepicker._script")
    <script>
        $('#form1').validate({
            rules: {
                "days": "required",
                "start_date_value": "required",

            }
        });
    </script>
@endsection
