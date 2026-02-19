@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت مشتریان")
@section("content")
    @include('component.input.datepicker.jalali_datepicker._style')
    <form id="form1" style="display: inline" action="{{route("contractor.definition.dashboard.update_software_system",$contractor)}}"
          method="post"
          novalidate="novalidate" autocomplete="off" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-12">

                    <div class="card">
                        <div class="card-header">
                            <h5> اطلاعات سامانه جامع </h5>
                        </div>
                        <div class="card-block">

                            @include("contractor.definition.dashboard._software_system")
                            <div class="col-md-12" >
                                <a href="{{route("contractor.definition.dashboard.index")}}"
                                   class="btn btn-outline-dark btn-lg">بازگشت</a>

                                <button type="submit" class="btn btn-primary btn-lg"> ذخیره</button>
                            </div>
                        </div>
                    </div>


                </div>





        </div>

    </form>
    @include('component.input.datepicker.jalali_datepicker._script')
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

                       "software_system_id": "required",
                        "api_url": "required",
                        "api_username": "required",
                        "api_password": "required",
                        "api_key": "required",

        }
        });


        $("#software_system_id").change(function () {
            software_system()
        });
        software_system()
        function software_system(){
            if ($("#software_system_id").val()!=0) {
                $(".software").show();
            } else {
                $(".software").hide();
            }
        }
    </script>
@endsection
