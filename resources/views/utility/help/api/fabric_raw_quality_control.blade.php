@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","راهنمای وب سرویس ها / راهنمای وب سرویس کنترل پارچه خام ")
@section("content")
    @include("utility.help.api._fabric_raw_start_quality_control")
    @include("utility.help.api._fabric_raw_new_defect")
    @include("utility.help.api._fabric_raw_end_quality_control")
    @include("utility.help.api._fabric_raw_error_list")
@endsection
@section("styles")
    <style>
        pre {
            text-align: left;
            direction: ltr;
        }
    </style>
@endsection


