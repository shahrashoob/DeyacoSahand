@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش  شیفت:  {{$shift->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.shift.update",$shift)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان شیفت ","value"=>$shift->caption])

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"active_status_id",
                                    "label"=>" وضعیت   ",
                                    "option"=>$status_option["items"],
                                    "text"=>$status_option["text"],
                                    "val"=>$status_option["value"],
                                    "class_col"=>""
                                    ])
                            </div>

                            @include("component.input._number",["id"=>"number_of_shift_work","label"=>"تعداد گروه شیفت","value"=>$shift->number_of_shift_work])
                            @include("component.input._number",["id"=>"number_of_shift_work_group","label"=>"تعداد دسته بندی شیفت","value"=>$shift->number_of_shift_work_group])
                            @include("component.input._number",["id"=>"legal_working_hours_in_minute","label"=>"میانگین ساعت کار قانونی در روز (دقیقه)","value"=>$shift->legal_working_hours_in_minute])

                        </div>

                        <a href="{{route("hr.shift.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره</button>

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
                "caption": "required",
                "active_status_id_auto": "required",
                "packing_type_label_printing_type_id_auto": "required",
                "weight": "required",
            }
        });
    </script>
@endsection
