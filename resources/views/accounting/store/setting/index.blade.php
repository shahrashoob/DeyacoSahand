
@extends('layouts.admin._master',["keypress_enable"=>1])

@section("content")
    @include('component.input.datepicker.jalali_datepicker._style')
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات اولیه سیستم</h5>
                </div>
                <div class="card-body p-0">
                    @include("utility.setting._tabs",["tab_name"=>"store"])

                </div>
            </div>
        </div>
    </div>
@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    @include('component.input.datepicker.jalali_datepicker._script')
@endsection