@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن  کمیته جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.definition.committee.store")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان کمیته ","value"=>$committee->caption])


                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"active_status_id",
                                    "label"=>" وضعیت   ",
                                    "option"=>$status_option["items"],
                                    "val"=>$committee->active_status->id??"",
                                    "text"=>$committee->active_status->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="col-md-9" data-select2-id="119">

                                @include("component.input.select2._select2",[
                               "id"=>"post_ids",
                               "label"=>" اعضای کمیته ",
                               "option"=>$post_option["items"],
                               "class_col"=>""
                               ])
                            </div>

                        </div>

                        <a href="{{route("hr.definition.committee.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت کمیته جدید</button>

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
