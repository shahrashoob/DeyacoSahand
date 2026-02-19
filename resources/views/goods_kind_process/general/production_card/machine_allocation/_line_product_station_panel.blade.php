@php $count_allocation=count($line_product_station->
                                    machine_type->
                                    getPossibilityOfAllocationMachine(false , 0 , $line_product_station));

                       $allocation_list_option=$line_product_station->machine_type->
                            getPossibilityOfAllocationMachine(true,0, $line_product_station)["items"]
@endphp
@if(count($allocation_list_option) > 0)
    <div class="col-xl-3 col-lg-12">
        <div class="card task-board-left">
            <div class="card-header">
                <h5>ماشین (های) {{$line_product_station->machine_type->caption}}</h5>
            </div>
            <div class="card-block">

                <div class="task-right">
                    <div class="taskboard-right-progress">
                        @php
                            $machine_count=$line_product_station->machine_type->machine->count();
                            $machine_on_count=$line_product_station->machine_type->getCountStatus("on_status");
                        @endphp
                        <h6 class="m-t-10">ماشین های روشن ({{$machine_on_count."/".$machine_count}})</h6>
                        <div class="progress">
                            <div class="progress-bar bg-success"
                                 role="progressbar"
                                 style="width: {{$machine_count==0?0:$machine_on_count/$machine_count*100}}%"
                                 aria-valuenow="{{$machine_count==0?0:$machine_on_count/$machine_count*100}}"
                                 aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        </div>


                        <h6 class="m-t-10"> آماده تخصیص ({{$count_allocation."/".$machine_count}})</h6>
                        <div class="progress">
                            <div class="progress-bar bg-c-purple"
                                 role="progressbar"
                                 style="width: {{$machine_count==0?0:$count_allocation/$machine_count}}%"
                                 aria-valuenow="{{$machine_count==0?0:$count_allocation/$machine_count}}"
                                 aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>

                <form id="form1" action="{{route($route_path,
                                                        [$production,$line_product_station->machine_type,$line_product_station,$machine_allocation??0])}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            @include("component.input._aotocomplet2",[
                                "id"=>"machine_type_".$line_product_station->machine_type->id,
                                "label"=>" تخصیص ماشین   ",
                                "option"=>$allocation_list_option,
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

@endif