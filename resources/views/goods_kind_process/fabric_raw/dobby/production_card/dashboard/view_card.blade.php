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


                    @include("goods_kind_process.fabric_raw.public._production_info_small")

                    @include("goods_kind_process.fabric_raw.production_card.dashboard._action")
                    @include("line_product_station.goods_kind.property._property_value",["product"=>$production->product])


                </div>
            </div>
        </div>

{{--        @include("goods_kind_process.fabric_raw.production_card.public.log_status")--}}

        @include("goods_kind_process.fabric_raw.public._machine_allocation_list")
        @include("goods_kind_process.public.production_log_status");
    </div>

@endsection

@section("scripts")
    <script>
        $("#btn_quality_control").click(function () {
            return confirm("آیا از تایید کنترل کیفیت محصولات تولید شده اطمینان دارید؟");
        })
        $("#btn_delivery_to_warehouse").click(function () {
            return confirm("آیا از تحویل کالا به انیار تولید  اطمینان دارید؟");
        })
    </script>
@endsection
