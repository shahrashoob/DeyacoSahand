@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن  بارکد عضویت جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.other.barcode_link.store")}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان بارکد ","value"=>$barcode_link->caption])

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"status_id",
                                    "label"=>" وضعیت بارکد    ",
                                    "option"=>$status_option["items"],
                                    "val"=>$status_option["value"],
                                    "text"=>$status_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                <div class="col-md-12 alert alert-info"> عضویت بارکدی دو نوع می باشد: عضویت سطح 1 مشتریان (جهت ثبت نام مشتریان عادی) و عضویت سطح 2 مشتریان (جهت عضویت مشتریان خرد، این نوع عضویت باید یک مشتری سطح 1  به عنوان معرف مشخص شود)</div>

                                @include("component.input._aotocomplet2",[
                                    "id"=>"customer_id",
                                    "label"=>" مشتری ( جهت عضویت بارکدی سطح 2)    ",
                                    "option"=>$customer_option["items"],
                                    "val"=>$customer_option["value"],
                                    "text"=>$customer_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                        </div>

                        <a href="{{route("utility.other.barcode_link.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت </button>

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
            }
        });
    </script>
@endsection
