@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])
@section("page_header_title","داشبورد انبار ")

@section('content')

    <div class="row">

        <div class="col-sm-12" id="panel_packing">
            <div class="card">
                <div class="card-header">
                    <h5> انبار گردانی {{$warehouse_handling->warehouse->caption}}
                        شماره: {{$warehouse_handling->getCode()}} </h5>
                </div>
                <div class="card-block">

                    @include("warehouse.warehouse_handling.dashboard._packing_form_list")
                </div>
                <div class="text-center">
                    {{$list->links('pagination::bootstrap-4')}}
                </div>

                <div class="center">
                    <a href="{{route("wh.warehouse_handling.add_packing_form.index",$warehouse_handling)}}"
                       class="btn btn-outline-dark">
                        بازگشت
                    </a>
                </div>


            </div>
        </div>


    </div>

@endsection
@section("styles")
    <style>

    </style>
@endsection
