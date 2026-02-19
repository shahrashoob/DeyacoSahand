@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">


        @include("goods_kind_process.fabric_raw.jacquard.machine.dashboard._input_info",["machine"=>$allocation->machine])

        <div class="col-md-12 center">
            <a href="{{route("fabric_raw.production_card.finished_allocation.index",$production)}}" class="btn btn-outline-dark">بازگشت</a>

        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
