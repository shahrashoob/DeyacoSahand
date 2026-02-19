@extends('layouts.admin._master')
@section("page_header_title","خدمات رفاهی / افزایش شارژ ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>خدمات رفاهی /  پرسنل

                    </h5>
                </div>
                <div class="card-body">
                    <form id="form1" action="{{route("accounting.welfare_service.tara.admin.dashboard.submit_charge",$welfare_service)}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="col-md-6">
                            <div class="row">
                                @include("component.input._lable",["id"=>"firstname","label"=>"نام ","value"=>$worker->firstname??""])
                                @include("component.input._lable",["id"=>"lastname","label"=>"نام و نام خانوادگی ","value"=>$worker->lastname??""])
                                @include("component.input._lable",["id"=>"date_of_birth","label"=>"تاریخ تولد ","value"=>$worker->date_of_birth??""])
                                @include("component.input._lable",["id"=>"national_code","label"=>"کد ملی ","value"=>$worker->national_code??""])
                                @include("component.input._number",["id"=>"amount","label"=>"مقدار افزایش شارژ (ریال) ","value"=>""])
                            </div>

                            <button type="submit" class="btn btn-primary">ثبت و ادامه</button>
                            <a href="{{route("accounting.welfare_service.tara.admin.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        </div>
                    </form>

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

            <script>

                $('#form1').validate({
                    rules: {
                        amount: "required",
                    }
                });

            </script>
@endsection
