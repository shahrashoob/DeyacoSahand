@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش تنظیمات ارزیابی عملکرد برای
                        {{$post->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("hr.post.post_evaluation.update",$post_evaluation)}}"
                          method="post" enctype="multipart/form-data"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"cron",'label'=>"دوره ارزیابی","value"=>$post_evaluation->cron])
                            @include("component.input._text",["id"=>"time_of_complete",'label'=>"مدت زمان تکمیل فرم ارزیابی","value"=>$post_evaluation->time_of_complete])
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                          "id"=>"active_status_id",
                                          "label"=>"وضعیت ",
                                           "option"=>$active_status_option["items"],
                                           "val"=>$post_evaluation->active_status->id??"",
                                           "text"=>$post_evaluation->active_status->caption??"",
                                           "class_col"=>""
                                           ])

                            </div>
                        </div>
                        <div class="w-100"><br/></div>

                        <a href="{{route("hr.post.post_evaluation.index",$post)}}"
                           class="btn btn-outline-dark">بازگشت</a>

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
                "time_of_complete": "required",
                "active_status_id_auto":"required",
            }
        });
    </script>
@endsection
