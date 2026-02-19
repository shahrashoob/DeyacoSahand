@extends('layouts.admin._master')
@section("page_header_title","گزارش 1005 - وضعیت ماشین آلات")

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>گزارش 1005 - وضعیت ماشین آلات</h5>
                </div>

                <div class="card-block">
                    <form id="form1" autocomplete="off" action="{{route("report.1005.submit")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            @include("report.1005._machine_type_permission")

                            @include("component.input.datepicker._datepicker",["id"=>"start_date","lable"=>"از تاریخ ","formatDate"=>"hh:mm:ss YYYY/MM/DD"])

                            @include("component.input.datepicker._datepicker",["id"=>"end_date","lable"=>" تا تاریخ ","formatDate"=>"hh:mm:ss YYYY/MM/DD"])


                        </div>

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary">مشاهده گزارش </button>

                    </form>
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
                "start_date_value":"required",
                "end_date_value":"required",
            }
        });
    </script>
@endsection
