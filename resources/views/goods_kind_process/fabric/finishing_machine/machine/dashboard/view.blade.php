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

                    @include("goods_kind_process.fabric.public._machine_info_small",["show_allocation_id"=>0])

                    @if($allocation)
                        @foreach($allocation->items as $item_machine_allocation)
                            @if($item_machine_allocation->status_id == 5310010)
                                <div class="row">
                                    @include("component.input._lable",["id"=>"","lable"=>" عملیات جاری","value"=>
                                    ($item_machine_allocation->line_product_station->station_operation->caption??"").
                                    " - ".
                                    ($item_machine_allocation->line_product_station->station_sub_operation->caption??"")
                                    ])
                                </div>
                                @break
                            @endif
                        @endforeach
                    @endif
                    @include("goods_kind_process.fabric.finishing_machine.machine.dashboard._action")


                </div>
            </div>
        </div>

        @include("goods_kind_process.fabric.finishing_machine.machine.dashboard._input_info")
        @include("goods_kind_process.fabric.finishing_machine.machine.dashboard._current_allocation_list")
        @include("goods_kind_process.fabric.finishing_machine.machine.dashboard._reserve_allocation")
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
