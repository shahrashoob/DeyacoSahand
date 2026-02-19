@extends('layouts.admin._master')

@section("page_header_title"," داشبورد تولید - بافندگی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم تولید {{$production_form->getCode()}}</h5>
                </div>
                <div class="card-block">

                    @include("goods_kind_process.fabric_raw.production_form._info_small")
                    @include("goods_kind_process.fabric_raw.production_form.dashboard._action")


                </div>
            </div>
        </div>


        @foreach($production_form->items as $item)

            @include("goods_kind_process.fabric_raw.production_form.dashboard._band_info",["production_form_item"=>$item])


        @endforeach

        @include("goods_kind_process.fabric_raw.production_form.dashboard._log")
    </div>


@endsection

@section("scripts")

@endsection
