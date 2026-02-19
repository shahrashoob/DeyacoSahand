@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")

    <div class="col-sm-12">
        {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
        <div class="card">
            <div class="card-header">
                <h5>
                    وضعیت های مجاز شاغلین برای ورود
                </h5>

            </div>
            <div class="card-block">

                <form id="form1" style="display: inline" action="{{route("hr.post.entry_status_permission.submit",$post)}}" method="post"
                      novalidate="novalidate">
                    @csrf

                    <div class="row">

                        @include("hr.post._entry_status_permission")

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
@endsection
