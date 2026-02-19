@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن  الگوی عملیات مالی جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("accounting.definition.financial_operation_pattern.update",$financial_operation_pattern)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان  ","value"=>$financial_operation_pattern->caption])
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"financial_operation_pattern_type_id",
                                    "label"=>" نوع الگوی عملیات مالی   ",
                                    "option"=>$financial_operation_pattern_type_option["items"],
                                    "val"=>$financial_operation_pattern_type_option["value"],
                                    "text"=>$financial_operation_pattern_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"register_detailed_code_for_customer",'label'=>"آیا کد تفضیلی مشتری در سند حسابداری ثبت شود.","checked"=>$financial_operation_pattern->register_detailed_code_for_customer])
                            </div>

                            @include("component.input._text",["id"=>"detailed_code_for_customer_number",'label'=>"کد تفضیلی مشتری در تفضیلی زیر ارسال گردد","value"=>$financial_operation_pattern->detailed_code_for_customer_number])


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
                "caption": "required",
                "code": "required",
                "status_id_auto": "required",
                "detailed_code_for_customer_number": {min:1,max:5},
            }
        });
    </script>
@endsection
