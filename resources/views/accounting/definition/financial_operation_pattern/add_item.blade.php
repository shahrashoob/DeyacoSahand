@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن  حساب به {{$financial_operation_pattern->caption}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("accounting.definition.financial_operation_pattern.submit_add_item",$financial_operation_pattern)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"account_id",
                                    "label"=>" حساب    ",
                                    "option"=>$account_option["items"],
                                    "val"=>$account_option["value"],
                                    "text"=>$account_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

<div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"financial_operation_pattern_item_type_id",
                                    "label"=>" نوع ارتباط حساب با الگوی مالی    ",
                                    "option"=>$financial_operation_pattern_item_types_option["items"],
                                    "val"=>$financial_operation_pattern_item_types_option["value"],
                                    "text"=>$financial_operation_pattern_item_types_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                        </div>

                        <a href="{{route("accounting.definition.financial_operation_pattern.index")}}" class="btn btn-outline-dark">بازگشت</a>

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
                "account_id_auto": "required",
                "financial_operation_pattern_item_type_id_auto": "required",
            }
        });
    </script>
@endsection
