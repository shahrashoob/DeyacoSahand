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
                <form id="form1" action="{{route("production.submit_form1",$production)}}" method="post" novalidate="novalidate">
                    @csrf

                    @include("production.production_card._info_small")


                    <a href="{{route("dashboard")}}" class="btn btn-outline-defualt">بازگشت</a>

                 @if($production->status_id!=500)
                    <a href="{{route("production.print_card",$production)}}" class="btn btn-info" >پرینت کردن کارت</a>

                 @else
                 <a href="{{route("production.form1",$production)}}" class="btn btn-success"> ثبت کارت </button>
                    <a href="{{route("production.replace",$production)}}" class="btn btn-danger" onclick="return confirm('آیا از جایگزین کردن کارت اطمینان دارید')" >جایگزین کردن کارت</a>

                 @endif


                </form>
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
