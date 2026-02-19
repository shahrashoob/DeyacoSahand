@extends('layouts.admin._master')

@section("page_header_title"," داشبورد طراحی - بافندگی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  فرم طراحی {{$design_form->getCode()}}</h5>
                </div>
                <div class="card-block">

                    @include("goods_kind_process.fabric_raw.design_form._info_small")
                    @include("goods_kind_process.fabric_raw.design_form.dashboard._action")

{{--                    @include("goods_kind_process.fabric_raw.machine.dashboard._action")--}}

                    {{--                    @if( $post_user->checkButtonPermission("production.500010") && $production->waiting_status_id==500010)--}}
                    {{--                        <a href="{{route("production.dashboard.request_material",$production)}}"--}}
                    {{--                           class="btn btn-primary">درخواست مواد اولیه </a>--}}
                    {{--                    @endif--}}


                </div>
            </div>
        </div>


    </div>

@endsection

@section("scripts")

@endsection
