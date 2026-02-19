@extends('layouts.admin._master')
@section("page_header_title","خدمات رفاهی / افزودن پرسنل جدید ")
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
                    <form id="form1" action="{{route("accounting.welfare_service.tara.admin.dashboard.submit_add_user",$worker)}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="col-md-6">
                            <div class="row">
                                @include("component.input._lable",["id"=>"firstname","label"=>"نام ","value"=>$worker->firstname??""])
                                @include("component.input._lable",["id"=>"lastname","label"=>"نام و نام خانوادگی ","value"=>$worker->lastname??""])
                                @include("component.input._lable",["id"=>"date_of_birth","label"=>"تاریخ تولد ","value"=>$worker->date_of_birth??""])
                                @include("component.input._lable",["id"=>"national_code","label"=>"کد ملی ","value"=>$worker->national_code??""])
                                @include("component.input._number",["id"=>"mobile","label"=>"شماره همراه ","value"=>$worker->mobile??""])
                            </div>

                                <button type="submit" class="btn btn-primary"> ثبت فرد جدید</button>
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
                firstname: "required",
                lastname: "required",
                father_name: "required",
                email: "required",
                country_id_auto: "required",
                username: {
                    required: true,
                },
                national_code: {
                    required: true,
                    number: true,
                },
                password: {minlength: 8, maxlength: 11},
                mobile: {required:true,minlength: 10, maxlength: 10},
                confirm_password: {equalTo: "#password"},
                date_of_contract:{  required: true,},
                exit_permit_status_id_auto:{  required: true,},
                entry_permit_status_id_auto:{  required: true,},
                status_id_auto:{  required: true,},
                date_of_contract_value:{  required: true,},
            }
        });

    </script>
@endsection
