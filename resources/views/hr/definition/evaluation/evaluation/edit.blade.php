@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش شاخص ارزیابی
                        {{$evaluation_indicator->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("hr.definition.evaluation.evaluation.update",$evaluation_indicator)}}"
                          method="post" enctype="multipart/form-data"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان شاخص ارزیابی","value"=>$evaluation_indicator->caption])
                            <div class="col-md-6">
                                @include("component.input._select",[

                                      "id"=>"evaluation_completion_type_id",
                                      "label"=>" نوع تکمیل   ",
                                      "option"=>$evaluation_completion_type["items"],
                                       "val"=>$evaluation_indicator->evaluation_completion_type->id??"",
                                        "text"=>$evaluation_indicator->evaluation_completion_type->caption??"",
                                        "class_col"=>""
                                      ])

                            </div>


                        </div>
                        <div class="w-100"><br/></div>

                        <a href="{{route("hr.definition.evaluation.evaluation.index")}}" class="btn btn-outline-dark">بازگشت</a>

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
                "evaluation_completion_type_id": "required",
            }
        });
    </script>
@endsection
