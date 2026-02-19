@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")

    <div class="col-sm-12">
        {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
        <div class="card">
            <div class="card-header">
                <h5> تنظیمات گفتگوی برخط - {{$post->code ." - ".$post->caption}} ({{$post->shift->caption??""}})
                    <a href="{{route("hr.post.index")}}" class="btn btn-dark btn-sm">بازگشت</a>
                </h5>

            </div>
            <div class="card-block">

                <form id="form1" style="display: inline" action="{{route("hr.post.chat.submit",$post)}}" method="post"
                      novalidate="novalidate">
                    @csrf

                    <div class="row">

                        @include("hr.post.chat._edit_chat_setting")

                    </div>
                </form>
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
