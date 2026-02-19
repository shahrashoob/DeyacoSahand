@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش شیء {{$smart_object->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.smart_object.update",$smart_object)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"نام شیء","value"=>$smart_object->caption])
                            @include("component.input._text",["id"=>"ip",'label'=>"IP","value"=>$smart_object->ip])
                            @include("component.input._text",["id"=>"port",'label'=>"Port","value"=>$smart_object->port])
                            @if(in_array($smart_object->smart_object_type_id,[1]))
                                @include("component.input._text",["id"=>"email",'label'=>"UserName","value"=>$smart_object->worker->email??""])
                                @include("component.input._text",["id"=>"password",'label'=>"Password","value"=>isset($smart_object->worker->email)?"********":""])
                            @endif

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"smart_object_type_id",
                                    "label"=>" نوع شیء هوشمند   ",
                                    "option"=>$smart_object_type_option["items"],
                                    "val"=>$smart_object->smart_object_type->id??"",
                                    "text"=>$smart_object->smart_object_type->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"status_id",
                                    "label"=>" وضعیت   ",
                                    "option"=>$status_option["items"],
                                    "val"=>$smart_object->status->id??"",
                                    "text"=>$smart_object->status->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>

                        </div>

                        <a href="{{route("utility.smart_object.index")}}" class="btn btn-outline-dark">بازگشت</a>

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
                "caption": "required",
                "ip": "required",
                "status_id_auto": "required",
                "smart_object_type_id_auto": "required",
                password: {
                    minlength: 8,
                },
                "email": "required",
            }
        });
    </script>
@endsection
