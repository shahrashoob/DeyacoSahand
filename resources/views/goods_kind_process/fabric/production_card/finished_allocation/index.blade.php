@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        @include("goods_kind_process.general.production_card.finish_allocation._list",["goods_kind_caption_en"=>"fabric"])
        <div class="col-md-12 center">
            <a href="{{route("fabric.production_card.view_card",$production)}}"
               class="btn btn-outline-dark">بازگشت</a>

        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
