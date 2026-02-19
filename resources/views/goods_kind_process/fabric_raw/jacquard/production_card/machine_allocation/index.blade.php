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
                    <a href="{{route("fabric_raw.production_card.view_card",$production)}}" class="btn btn-outline-dark">بازگشت</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-12">
            <div class="card task-board-left">
                <div class="card-header">
                    <h5>ماشین (های) {{$machine_type->caption}}</h5>
                </div>
                <div class="card-block">

                    <div class="task-right">
                        <div class="taskboard-right-progress">
                            @php
                                $machine_count=$machine_type->machine->count();
                                $machine_on_count=$machine_type->getCountStatus("on_status");
                            @endphp
                            <h6 class="m-t-10">ماشین های روشن ({{$machine_on_count."/".$machine_count}})</h6>
                            <div class="progress">
                                <div class="progress-bar bg-success"
                                     role="progressbar"
                                     style="width: {{$machine_on_count/$machine_count*100}}%"
                                     aria-valuenow="{{$machine_on_count/$machine_count*100}}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>



                            @php $count_allocation=count($machine_type->
                                    getPossibilityOfAllocationMachine());
                            @endphp

                            <h6 class="m-t-10"> آماده تخصیص ({{$count_allocation."/".$machine_count}})</h6>
                            <div class="progress">
                                <div class="progress-bar bg-c-purple"
                                     role="progressbar"
                                     style="width: {{$count_allocation/$machine_count}}%"
                                     aria-valuenow="{{$count_allocation/$machine_count}}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    <form id="form1" action="{{route("fabric_raw.dobby.machine_allocation.select_band",
                                                        [0,$machine_type,$production,1,true])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"machine_type_".$machine_type->id,
                                    "label"=>" تخصیص ماشین   ",
                                    "option"=>$machine_type->
                                    getPossibilityOfAllocationMachine(true)["items"],
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>""
                                    ])
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">تخصیص ماشین</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

@endsection
