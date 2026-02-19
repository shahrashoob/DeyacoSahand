@extends('layouts.admin._master')

@section('page_header_title',"داشبورد جاری تولید -  ".$production->product->goods_kind->caption)

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> کارت تولید {{$production->serial()}}</h5>
                </div>
                <div class="card-block">
                    @include("production.public._production_info_small")
                    <a href="{{route("warps.production_card.view_card",$production)}}" class="btn btn-outline-dark">بازگشت</a>
                </div>
            </div>
        </div>
        @include("goods_kind_process.general.production_card.machine_allocation._machine_type_for_allocation",["route_path"=>"fabric_raw.machine_allocation.select_machine_type"])

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

@endsection
