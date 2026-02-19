@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}

            <div class="col-sm-12">
                <h5>افزودن شاغل جدید </h5>

                <hr>
                <ul class="nav nav-tabs" id="myTab" role="tablist">

                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="edit-tab" data-toggle="tab" href="#edit" role="tab"
                           aria-controls="contact" aria-selected="false">ویرایش اطلاعات کاربر</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">

                    <div class="tab-pane  active" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                        @include("hr.worker._edit_worker_info")
                    </div>
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
