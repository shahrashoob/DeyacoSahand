@extends('layouts.admin._master')
@section("page_header_title","داشبورد فروش")
@section("content")
    <div class="row">


        @include("sales.public._log")

    </div>
    <div class="text-center">
        <a href="{{route("sales.dashboard.index")}}" class="btn btn-dark">بازگشت</a>
    </div>

    </div>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
