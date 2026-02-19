@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن  مرکز هزینه جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.employment.admin.contractor.cost_center.store",$employment)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"code",'label'=>"کد مرکز هزینه","value"=>$cost_center->code])
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان مرکز هزینه ","value"=>$cost_center->caption])

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"status_id",
                                    "label"=>" وضعیت   ",
                                    "option"=>$status_option["items"],
                                    "val"=>$cost_center->status->id??"",
                                    "text"=>$cost_center->status->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>

                        </div>

                        <a href="{{route("hr.employment.admin.contractor.registration_cost_center.index",$employment)}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت مرکز هزینه جدید</button>

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
                "code": "required",
                "status_id_auto": "required",
            }
        });
    </script>
@endsection
