@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش    {{$production_form_status->status->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("production.form_status.update",$production_form_status)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"main_status_id",
                                    "label"=>" گروه اصلی وضعیت ",
                                    "option"=>$main_status_option["items"],
                                    "val"=>$production_form_status->status->main_status->id??"",
                                    "text"=>$production_form_status->status->main_status->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان وضعیت  ","value"=>$production_form_status->status->caption])
                            <div class="w-100"></div>
                        </div>
                        <a href="{{route("production.waiting_status.index",$production_form_status->goods_kind_id)}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary">  ذخیره تغییرات </button>

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
                "goods_kind_id_auto": "required",
            }
        });
    </script>
@endsection
