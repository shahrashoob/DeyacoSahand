@extends('layouts.admin._master')

@section("page_header_title"," داشبورد تولید کالا ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم تولید {{$production_form->getCode()}}</h5>
                </div>
                <div class="card-block">

                    @include("production.production_form.public._info_small")
                    @include("goods_kind_process.fabric.production_form._action")


                </div>
            </div>
        </div>


        @foreach($production_form->items as $item)

            @include("production.production_form.public._band_info",[
	                "production_form_item"=>$item,
                    "action_band_view_path"=>null
                    ])


        @endforeach

        @include("production.production_form.public._log")
    </div>


@endsection

@section("scripts")

@endsection
