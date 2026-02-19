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
                    @include("production.public._production_info_small")
                    <a href="{{route("fabric_raw.production_card.view_card",$production)}}" class="btn btn-outline-dark">بازگشت</a>
                </div>
            </div>
        </div>

        @include("goods_kind_process.general.production_card.machine_allocation._machine_type_for_allocation",["route_path"=>"fabric_raw.machine_allocation.select_machine_type"])

        {{--        @foreach($production->product->line_product_station()->where("status_id",1200)->get() as $line_product_station)--}}
{{--            <div class="col-xl-3 col-lg-12">--}}
{{--                <div class="card task-board-left">--}}
{{--                    <div class="card-header">--}}
{{--                        <h5>ماشین (های) {{$line_product_station->machine_type->caption}}</h5>--}}
{{--                    </div>--}}
{{--                    <div class="card-block">--}}

{{--                        <div class="task-right">--}}
{{--                            <div class="taskboard-right-progress">--}}
{{--                                @php--}}
{{--                                    $machine_count=$line_product_station->machine_type->machine->count();--}}
{{--                                    $machine_on_count=$line_product_station->machine_type->getCountStatus("on_status");--}}
{{--                                @endphp--}}
{{--                                <h6 class="m-t-10">ماشین های روشن ({{$machine_on_count."/".$machine_count}})</h6>--}}
{{--                                <div class="progress">--}}
{{--                                    <div class="progress-bar bg-success"--}}
{{--                                         role="progressbar"--}}
{{--                                         style="width: {{$machine_count==0?0:$machine_on_count/$machine_count*100}}%"--}}
{{--                                         aria-valuenow="{{$machine_count==0?0:$machine_on_count/$machine_count*100}}"--}}
{{--                                         aria-valuemin="0"--}}
{{--                                         aria-valuemax="100"></div>--}}
{{--                                </div>--}}



{{--                                @php $count_allocation=count($line_product_station->--}}
{{--                                    machine_type->--}}
{{--                                    getPossibilityOfAllocationMachine());--}}
{{--                                @endphp--}}

{{--                                <h6 class="m-t-10"> آماده تخصیص ({{$count_allocation."/".$machine_count}})</h6>--}}
{{--                                <div class="progress">--}}
{{--                                    <div class="progress-bar bg-c-purple"--}}
{{--                                         role="progressbar"--}}
{{--                                         style="width: {{$machine_count==0?0:$count_allocation/$machine_count}}%"--}}
{{--                                         aria-valuenow="{{$machine_count==0?0:$count_allocation/$machine_count}}"--}}
{{--                                         aria-valuemin="0"--}}
{{--                                         aria-valuemax="100"></div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}


{{--                        <form id="form1" action="{{route("fabric_raw.machine_allocation.select_machine_type",--}}
{{--                                                        [$production,$line_product_station->machine_type])}}"--}}
{{--                              method="post"--}}
{{--                              autocomplete="off"--}}
{{--                              novalidate="novalidate">--}}
{{--                            @csrf--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-12">--}}
{{--                                    @include("component.input._aotocomplet2",[--}}
{{--                                        "id"=>"machine_type_".$line_product_station->machine_type->id,--}}
{{--                                        "label"=>" تخصیص ماشین   ",--}}
{{--                                        "option"=>$line_product_station->machine_type->--}}
{{--                                        getPossibilityOfAllocationMachine(true)["items"],--}}
{{--                                        "val"=>"",--}}
{{--                                        "text"=>"",--}}
{{--                                        "class_col"=>""--}}
{{--                                        ])--}}
{{--                                </div>--}}

{{--                            </div>--}}
{{--                            <button type="submit" class="btn btn-primary">تخصیص ماشین</button>--}}
{{--                        </form>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        @endforeach--}}

    </div>

@endsection

@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

@endsection
