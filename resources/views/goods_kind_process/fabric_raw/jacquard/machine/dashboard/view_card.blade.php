@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">

                    @include("line_product_station.machine.public._info_small")

                    @include("goods_kind_process.fabric_raw.jacquard.machine.dashboard._action")

                    {{--                    @if( $post_user->checkButtonPermission("production.500010") && $production->waiting_status_id==500010)--}}
                    {{--                        <a href="{{route("production.dashboard.request_material",$production)}}"--}}
                    {{--                           class="btn btn-primary">درخواست مواد اولیه </a>--}}
                    {{--                    @endif--}}


                </div>
            </div>
        </div>

@include("goods_kind_process.fabric_raw.jacquard.machine.dashboard._input_info")
@include("goods_kind_process.fabric_raw.jacquard.machine.dashboard._reserve_allocation")
@include("goods_kind_process.fabric_raw.jacquard.machine.dashboard._form_list")
    </div>

@endsection

@section("styles")
<style>
    th{
        vertical-align: middle !important;
    }
</style>
@endsection
@section("scripts")

@endsection
