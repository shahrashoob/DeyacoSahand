@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن تعرفه جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("accounting.tariff.store")}}" method="post" autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان تعرفه","value"=>$tariff->caption])

                                <div class="col-md-6">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"currency_id",
                                        "label"=>" واحد پول  ",
                                        "option"=>$currencies_option["items"],
                                        "val"=>$tariff->curency->id??"",
                                        "text"=>$tariff->curency->caption??"",
                                        "class_col"=>""
                                        ])
                                </div>
                            @include("component.input.datepicker._datepicker",["id"=>"start_datetime","lable"=>"از تاریخ "])

                            @include("component.input.datepicker._datepicker",["id"=>"end_datetime","lable"=>"تا تاریخ "])


                        </div>

                        <a href="{{route("accounting.tariff.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت تعرفه جدید</button>

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
                "start_datetime_value": "required",
                "end_datetime_value": "required",
                "currency_id_auto": "required"
            }
        });
    </script>
@endsection
