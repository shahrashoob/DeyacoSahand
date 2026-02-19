@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")

    <form id="form1" style="display: inline" action="{{route("hr.post.update.info_setting",$post)}}" method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">

            <div class="col-sm-12">
                {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}

                <div class="col-sm-12">
                    <h5> قوانین دیجیتال پست {{$post->code ." - ".$post->caption}} ({{$post->shift->caption??""}})
                        <a href="{{route("hr.post.index")}}" class="btn btn-dark btn-sm">بازگشت</a>
                    </h5>
                    <hr>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">

{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link text-uppercase show active" id="entry_status-tab" data-toggle="tab"--}}
{{--                               href="#entry_status" role="tab"--}}
{{--                               aria-controls="contact" aria-selected="false">وضعیت های مجاز شاغلین برای ورود </a>--}}
{{--                        </li>--}}
                        <li class="nav-item">
                            <a class="nav-link text-uppercase show active" id="traffic_setting-tab" data-toggle="tab"
                               href="#traffic_setting" role="tab"
                               aria-controls="contact" aria-selected="false">تنظیمات تردد </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab"
                               aria-controls="contact" aria-selected="false">تنظیمات جانشین</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="leave-tab" data-toggle="tab" href="#leave" role="tab"
                               aria-controls="contact" aria-selected="false">تنظیمات مرخصی</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="tab5-tab" data-toggle="tab" href="#tab5" role="tab"
                               aria-controls="contact" aria-selected="false">تنظیمات اضافه کاری</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="tab6-tab" data-toggle="tab" href="#tab6" role="tab"
                               aria-controls="contact" aria-selected="false">تنظیمات ماموریت</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="tab7-tab" data-toggle="tab" href="#tab7" role="tab"
                               aria-controls="contact" aria-selected="false">تنظیمات جابجایی شیفت</a>
                        </li>


                    </ul>
                    <div class="tab-content" id="myTabContent">

{{--                        <div class="tab-pane fade show active" id="entry_status" role="tabpanel"--}}
{{--                             aria-labelledby="entry_status-tab">--}}
{{--                            @include("hr.post._entry_status_permission")--}}
{{--                        </div>--}}
                        <div class="tab-pane fade show active" id="traffic_setting" role="tabpanel"
                             aria-labelledby="traffic_setting-tab">
                            @include("hr.post._traffic_setting_permission")
                        </div>
                        <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                            @include("hr.post._edit_post_replace_info")
                        </div>
                        <div class="tab-pane fade" id="leave" role="tabpanel" aria-labelledby="leave-tab">
                            @include("hr.post._edit_leave_info")
                        </div>
                        <div class="tab-pane fade" id="tab5" role="tabpanel" aria-labelledby="tab5-tab">
                            @include("hr.post._edit_work_overtime_info")
                        </div>
                        <div class="tab-pane fade" id="tab6" role="tabpanel" aria-labelledby="tab6-tab">
                            @include("hr.post._edit_mission_info")
                        </div>
                        <div class="tab-pane fade" id="tab7" role="tabpanel" aria-labelledby="tab7-tab">
                            @include("hr.post._edit_replacement_info")
                        </div>


                    </div>
                </div>
            </div>

        </div>
        @include("component.input._hidden",["id"=>"last_tab_open","value"=>isset($last_tab_open)?$last_tab_open:"home"])
    </form>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

    <script>

        $('#form1').validate({
            rules: {
                caption: "required",
                role_id_auto: "required",
                chart_id_auto: "required",
                shift_id_auto: "required",
            }
        });

        $("#for_leave_required_to_replace_person").change(function () {

            $("#post_replace_list").css("display", $("#for_leave_required_to_replace_person").is(":checked") ? "inline" : "none")

        })
        $("#can_chat_with_posts").change(function () {

            $("#post_chat_list").css("display", $("#can_chat_with_posts").is(":checked") ? "inline" : "none")

        })

    </script>
@endsection
