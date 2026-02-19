@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>سابقه {{$machine_allocation_modification->machine_allocation_modification_type->caption}} {{$machine_allocation_modification->warehouse->caption}} -
                        شماره {{$machine_allocation_modification->id}} -

                    </h5>
                </div>
                <div class="card-block" style="overflow: auto">

                    <div id="panel_change_grade">
                        <div class="row">

                            @include("goods_kind_process.general.machine.material_return_to_warehouse._change_grade_list",["modification_temp"=>$machine_allocation_modification,"before_degree"=>false])

                        </div>

                        <div class="row">

                            @include("goods_kind_process.general.machine.material_return_to_warehouse._waste_list",["modification_temp"=>$machine_allocation_modification,"before_degree"=>false])

                        </div>

                        <div class="row">

                            @include("goods_kind_process.general.machine.material_return_to_warehouse._packing_form_list",["modification_temp"=>$machine_allocation_modification,"before_amount"=>true,"allow_print"=>true,"caption_list"=>"لیست بسته بندی ها"])

                        </div>


                        <div class="row">

                            @include("goods_kind_process.general.machine.material_return_to_warehouse._form_list")

                        </div>


                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-12 center">
            <a class="btn btn-outline-dark" href="{{route($route_path."index",$machine)}}">بازگشت</a>
        </div>


    </div>

@endsection
@section("styles")

@endsection
