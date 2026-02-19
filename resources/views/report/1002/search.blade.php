@extends('layouts.admin._master')

@section('page_header_title'," کارتابل جاری تولید")

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>فرم ارزیابی عملکرد تولید</h5>
            </div>
            <div class="card-block">


                    @include("production.production_card_._info_small")

                    <a href="{{route("report.1002.index")}}" class="btn btn-outline-defualt">بازگشت</a>
                    <a href="{{route("production.print_card",$production)}}" class="btn btn-info" >پرینت کردن کارت</a>



            </div>
        </div>
    </div>

</div>

@endsection
@section("styles")
@include("component.input.datepicker._script")
@endsection

@section("scripts")
<script>
 $('#form1').validate({
            rules: {
                shift_time: "required",
                set_up_time: "required",
                unemployment_time: "required",
                down_time: "required",
                line_code: "required",
                start_time_m: "required",
                start_time_h: "required",
                end_time_m: "required",
                end_time_h: "required",
                number_product: "required",
                sub_number_product: "required",
                date_of_production_date: "required",
            }
        });
</script>
@endsection
