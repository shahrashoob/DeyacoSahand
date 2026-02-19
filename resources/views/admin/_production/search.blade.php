@extends('layouts.admin._master')

@section('page_header_title'," ")

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>فرم ارزیابی عملکرد تولید</h5>
            </div>
            <div class="card-block">
                <form id="form1" action="{{route("submit_form1",$production)}}" method="post" novalidate="novalidate">
                    @csrf

                    @include("admin.production._info")

                 @if(Str::of($production->status)->trim()!="Waiting for production")
                    <a href="{{route("production.print",$production)}}" class="btn btn-info" >پرینت کردن کارت</a>

                 @else
                 <a href="{{route("cancel",$production)}}" class="btn btn-danger" onclick="return confirm('آیا از کنسل کردن کارت اطمینان دارید')" >کنسل کردن کارت</a>

                 <a href="{{route("form1",$production)}}" class="btn btn-primary"> ثبت کارت </button>

                 @endif
                 <a href="{{route("dashboard")}}" class="btn btn-outline-defualt">بازگشت</a>

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
