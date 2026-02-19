@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن آموزش جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route('hr.definition.education.education.store')}}" method="post"
                          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان آموزش","value"=>""])
                            @include("component.input._aotocomplet2",[

                                "id"=>"education_type_id",
                                "label"=>" نوع آموزش   ",
                                "option"=>$educations_option["items"],
                                ])
                            @include("component.input._aotocomplet2",[

                                   "id"=>"exam_type_id",
                                   "label"=>" نوع سنجش   ",
                                   "option"=>$educations_exam_option["items"],
                                  ])
                            @include("component.input._file_upload",["id"=>"educational_text_file_id",'label'=>"محتوای متنی آموزشی"])
                            @include("component.input._file_upload",["id"=>"educational_video_file_id",'label'=>"ویدیو آموزشی"])
                            @include("component.input._text",["id"=>"minimum_score_to_confirm_the_education",'label'=>"حداقل امتیاز جهت تایید آموزش(0-100)"])

                        </div>
                        <a href="{{route('hr.definition.education.education.index')}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> افزودن</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "education_type_id_auto": "required",
                "exam_type_id_auto": "required",
            }
        });
    </script>
@endsection
