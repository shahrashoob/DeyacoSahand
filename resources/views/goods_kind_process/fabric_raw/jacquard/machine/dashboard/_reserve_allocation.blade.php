@if( count($reserve_allocation_list) > 0)

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>کارت (های) تولید رزرو
                </h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center!important;">
                        <thead>
                        <tr>

                            <th>اولویت</th>
                            <th>کارت تولید</th>
                            <th> کالا</th>
                            <th></th>
                            <th>مقدار</th>
                            <th>تاریخ رزرو</th>
                            <th>پیش بینی مدت زمان تولید
                                <br/>
                                (عملی/ ساعت)
                            </th>
                            <th>پیش بینی مدت زمان تولید
                                <br/>
                                (تئوری/ ساعت)
                            </th>
                            <th> پیش بینی تاریخ شروع تولید
                                <br/>
                                (عملی)
                            </th>
                            <th> پیش بینی تاریخ شروع تولید
                                <br/>
                                (تئوری)
                            </th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($reserve_allocation_list as $item)
                            <tr>
                                <td
                                        @if($item->other_allocation_count) style="vertical-align: middle"  rowspan="{{$item->other_allocation_count+1}}" @endif
                                >{{$item->allocation->priority_number}}</td>
                                <td>

                                    <a href="{{route("fabric_raw.production_card.view_card",$item->production)}}">{{$item->production->serial()}}
                                        @if($item->production->production_type_id ==2)
                                            <i class="fa fa-vial text-dark"></i>
                                        @endif
                                    </a>


                                </td>
                                <td>
                                    {{$item->product->fullCaption()}}

                                </td>
                                <td>
                                    @if($item->version_code)
                                        V{{$item->version_code}}
                                    @endif
                                </td>
                                <td>
                                    @include("line_product_station.product.unit_of_measure_type._machine_allocation",["production"=>$item->production,"units"=>$item->production->get_allocation_amount($machine->id,1,$item->allocation_id,true,1000) ])
                                </td>
                                <td>
                                    {{$item->get_datetime()}}
                                </td>
                                <td>
                                    {{$item->allocation->partical_houre()}}
                                </td>
                                <td>
                                    {{$item->allocation->theory_houre()}}
                                </td>
                                <td>
                                    {{$item->allocation->get_partical_datetime()}}
                                </td>
                                <td>
                                    {{$item->allocation->get_theory_datetime()}}
                                </td>
                                <th>
                                    <a href="{{route("fabric_raw.jacquard.machine.allocation_card.index",[$machine,$item->allocation_id])}}">
                                        <i class="fa fa-print"></i>
                                    </a>

                                </th>


                            </tr>
                            @if($item->other_allocation_count)
                                @for($k=1; $k<= $item->other_allocation_count; $k++)
                                    @php $key="other_allocation_".$k; $other_machine_allocation=$item->$key; @endphp
                                    <tr>

                                        <td>

                                            <a href="{{route("fabric_raw.production_card.view_card",$other_machine_allocation->production)}}">{{$other_machine_allocation->production->serial}}
                                                @if($other_machine_allocation->production->production_type_id ==2)
                                                    <i class="fa fa-vial text-dark"></i>
                                                @endif
                                            </a>


                                        </td>
                                        <td>
                                            {{$other_machine_allocation->product->fullCaption()}}

                                        </td>
                                        <td>
                                            @if($other_machine_allocation->version_code)
                                                V{{$other_machine_allocation->version_code}}
                                            @endif
                                        </td>
                                        <td>
                                            @include("line_product_station.product.unit_of_measure_type._machine_allocation",["production"=>$item->production,"units"=>$item->production->get_allocation_amount($machine->id,1,$item->allocation_id,true,1000) ])
                                        </td>
                                        <td colspan="5">

                                        </td>

                                        <th>
                                            <a href="{{route("fabric_raw.jacquard.machine.allocation_card.index",[$machine,$item->allocation_id])}}">
                                                <i class="fa fa-print"></i>
                                            </a>

                                        </th>


                                    </tr>
                                @endfor
                            @endif
                        @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>

@endif
