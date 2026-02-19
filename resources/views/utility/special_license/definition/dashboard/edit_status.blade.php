@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  ویرایش وضعیت {{$special_license_type->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("utility.special_license.definition.dashboard.update_status",$special_license_type)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="col-md-4">
                        @include("component.input._aotocomplet2",[
                                                  "id"=>"active_status_id",
                                                  "label"=>" وضعیت   ",
                                                  "option"=>$status_option["items"],
                                                  "val"=>$special_license_type->active_status->id??"",
                                                  "text"=>$special_license_type->active_status->caption??"",
                                                  "class_col"=>""
                                                  ])
                        </div>
                        <div class="col-md-4">
                        @include("component.input._aotocomplet2",[
                                                  "id"=>"sms_status_id",
                                                  "label"=>" وضعیت ارسال پیامک  ",
                                                  "option"=>$sms_status_option["items"],
                                                  "val"=>$special_license_type->sms_status->id??"",
                                                  "text"=>$special_license_type->sms_status->caption??"",
                                                  "class_col"=>""
                                                  ])
                        </div>
                        <div class="col-md-4">
                        <a href="{{route("utility.special_license.definition.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                        </div>
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
    @include("component.input.select2._script")
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "status_id_auto": "required",
            }
        });
    </script>
@endsection
