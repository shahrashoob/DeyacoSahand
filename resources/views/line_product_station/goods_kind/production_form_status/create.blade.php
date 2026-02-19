@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن وضعیت فرم جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("production.form_status.store",$goods_kind)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"main_status_id",
                                    "label"=>" گروه اصلی وضعیت ",
                                    "option"=>$main_status_option["items"],
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان وضعیت جدید ","value"=>$status ->caption])
                            <div class="w-100"></div>
                        </div>

                        <a href="{{route("production.form_status.index",$goods_kind)}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت وضعیت جدید</button>

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
                "caption": "required", "goods_kind_id_auto": "required",
            }
        });
    </script>
@endsection
