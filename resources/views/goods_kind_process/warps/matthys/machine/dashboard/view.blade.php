@extends('layouts.admin._master')

@section('page_header_title',"داشبورد ماشین آلات")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">

                    @include("line_product_station.machine.public._info_small")
                    @include("goods_kind_process.warps.matthys.machine.dashboard._action")


                </div>
            </div>
        </div>

        @include("goods_kind_process.warps.matthys.machine.dashboard._input_info")
        @include("goods_kind_process.warps.matthys.machine.dashboard._reserve_allocation",["module_route_name"=>"matthys"])
    </div>

@endsection

@section("styles")
    <style>
        th {
            vertical-align: middle !important;
        }
    </style>
@endsection
@section("scripts")

@endsection
