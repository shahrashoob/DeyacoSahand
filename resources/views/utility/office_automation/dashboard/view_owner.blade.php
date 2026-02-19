@extends('layouts.admin._master')
@section('page_header_title'," اتوماسیون اداری"." / کار ".$office_automation_work->code)
@section("content")

    <div class="page-wrapper">

        <div class="row">

{{--            <div class="col-xl-3 col-lg-12">--}}
{{--                @include("utility.office_automation.dashboard._task_right")--}}
{{--            </div>--}}
            <div class="col-xl-12 col-lg-12 ">

                <div class="row">
{{--                    <div class="col-md-12 col-sm-12">--}}
{{--                        <div class="card ">--}}
{{--                            <div class="card-header">--}}
{{--                                <h5>  {{$office_automation_work->caption}}</h5>--}}
{{--                            </div>--}}
{{--                            <div class="card-block card-task">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-sm-12">--}}
{{--                                        {!! $office_automation_work->description !!}--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <hr/>--}}
{{--                                <div class="task-board">--}}
{{--                                    @if(!$work_parent_id)--}}
{{--                                    <a href="{{route("utility.office_automation.dashboard.index")}}"--}}
{{--                                       class="btn btn-outline-dark">بازگشت</a>--}}
{{--                                    @else--}}

{{--                                        <a href="{{route("utility.office_automation.dashboard.view",$work_parent_id)}}"--}}
{{--                                           class="btn btn-outline-dark">بازگشت</a>--}}
{{--                                        @endif--}}

                                        <a href="{{route("utility.office_automation.dashboard.create",[0,$office_automation_work])}}"
                                           class="btn btn-primary">ایجاد کار جدید</a>


{{--                                    @foreach($office_automation_work->files as $office_automation_file)--}}
{{--                                        <a href="{{route("utility.office_automation.dashboard.download",[$office_automation_work,$office_automation_file])}}" class="">--}}
{{--                                            <i class="feather icon-paperclip "></i>--}}
{{--                                            {{$office_automation_file->file->caption}}--}}
{{--                                        </a>--}}
{{--                                    @endforeach--}}
{{--                                </div>--}}

{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    @include("utility.office_automation.dashboard._to_do_list")
                </div>
            </div>

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")

    <script>

        $('.form_to_do').validate({
            rules: {
                description: "required",

            }
        });
    </script>
@endsection
