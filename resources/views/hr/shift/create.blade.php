@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن شیفت جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.shift.store")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان شیفت ","value"=>""])

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"active_status_id",
                                    "label"=>" وضعیت   ",
                                    "option"=>$status_option["items"],"class_col"=>""
                                    ])
                            </div>

                            @include("component.input._number",["id"=>"number_of_shift_work","label"=>" تعداد گروه شیفت","value"=>"1"])
                            @include("component.input._number",["id"=>"legal_working_hours_in_minute","label"=>"میانگین ساعت کار قانونی در روز (دقیقه)","value"=>"440"])

                        </div>

                        <a href="{{route("hr.shift.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")


    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "active_status_id_auto": "required",
                "number_of_shift_work": {required:true,min:1,max:{{$max_shift_work_count}}},
            }
        });
    </script>
@endsection
