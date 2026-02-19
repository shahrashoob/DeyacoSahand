@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش  {{$script->code." - ".$script->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.script.".$script->code.".update",$script)}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">

                                    @include("utility.script.script._init_info")

                                    @include("component.input._number",["id"=>"before_days_from",'label'=>"از چند روز قبل ( از تاریخ جاری)","value"=>isset($data["before_days_from"])?$data["before_days_from"]:"","class_col"=>"col-md-12"])
                                    @include("component.input._number",["id"=>"before_days_to",'label'=>"تا چند روز قبل ( از تاریخ جاری)","value"=>isset($data["before_days_to"])?$data["before_days_to"]:"","class_col"=>"col-md-12"])


                                </div>
                            </div>
                            @include("utility.script.script._cron")

                            @include("utility.script.1006._panels")
                        </div>

                        <br/>
                        <br/>
                        <a href="{{route("utility.script.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

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
                "cron": "required",
                "active_status_id_auto": "required"
            }
        });
    </script>
@endsection
