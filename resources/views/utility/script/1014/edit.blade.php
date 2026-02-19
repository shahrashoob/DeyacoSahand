@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش {{$script->code." - ".$script->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.script.".$script->code.".update",$script)}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">

                                    @include("utility.script.script._init_info")

                                    @include("component.input._number",["id"=>"time_of_waning_1",'label'=>"زمان هشدار سطح 1 (ساعت)","value"=>isset($data["time_of_waning_1"]) ?$data["time_of_waning_1"]:"" ,"class_col"=>"col-md-12"])
                                    @include("component.input._number",["id"=>"time_of_waning_2",'label'=>"زمان هشدار سطح 2 (ساعت)","value"=>isset($data["time_of_waning_2"]) ?$data["time_of_waning_2"]:"" ,"class_col"=>"col-md-12"])
                                    @include("component.input._number",["id"=>"time_of_waning_3",'label'=>"زمان هشدار سطح 3 (ساعت)","value"=>isset($data["time_of_waning_3"]) ?$data["time_of_waning_3"]:"" ,"class_col"=>"col-md-12"])

                                    @include("component.input._radio",["id"=>"type_of_calculation_hours","label"=>"روش محاسبه  زمان تولید","radios"=>[
                                        ["value"=>"theory","label"=>"پیش بینی زمان تثوری","checked"=>isset($data["type_of_calculation_hours"]) && $data["type_of_calculation_hours"]=="theory"],
                                    ["value"=>"practical","label"=>"پیش بینی زمان عملی","checked"=>isset($data["type_of_calculation_hours"]) && $data["type_of_calculation_hours"]=="practical"]]]
                                    )
                                </div>
                            </div>
                            @include("utility.script.script._cron")
                            <div class="col-md-12">
                                <h5>تنظیمات خاص اسکریپت</h5>
                                @include("utility.script.".$script->code."._panels")
                            </div>

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
                "active_status_id_auto": "required",
                "time_of_waning_1": "required",
                "time_of_waning_2": "required",
                "time_of_waning_3": "required",
            }
        });
    </script>
@endsection
