@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')

    <form id="form1" action="{{route('hr.employment.admin.personal.financial_information.submit',$employment)}}"
          method="post"
          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
        @csrf

        @include('hr.employment.admin.personal.financial_information._register')


    </form>

@endsection

@section("styles")

    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "detailed_code": "required",
                "cost_center_id_auto": "required",
            }
        });
    </script>
@endsection
