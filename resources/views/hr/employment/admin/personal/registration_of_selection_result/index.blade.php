@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    @include('component.input.datepicker.jalali_datepicker._script')
    <form id="form1" action="{{route('hr.employment.admin.personal.registration_of_selection_result.submit',$employment)}}"
          method="post"
          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
        @csrf

        @include('hr.employment.admin.personal.registration_of_selection_result._register')
        @if($allow_contrac_detail)
            @include('hr.employment.admin.personal.registration_of_selection_result._contract_detail')
        @endif

        <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}" class="btn btn-outline-dark">بازگشت</a>

        <button type="submit" class="btn btn-primary">تایید</button>


    </form>

@endsection

@section("styles")
    @include("component.input.datepicker.jalali_datepicker._style")
    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    @include("component.input.datepicker.jalali_datepicker._script")
    <script>
        $('#form1').validate({
            rules: {
                "score_obtained_to_confirm_selection": "required",
                "right_to_work": "required",
                "start_date_of_contract_value": "required",
                "end_date_of_contract_value": "required",
                "absorption_type_id":"required",
            }
        });
    </script>
@endsection
