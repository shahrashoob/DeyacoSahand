
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
                        <thead >
                        <tr>

                            <th>اولویت</th>
                            <th>کارت تولید</th>
                            <th> کالا</th>
                            <th>مقدار</th>
                            <th>تاریخ رزرو</th>
                            <th>پیش بینی مدت زمان تولید
                                <br/>
                                (عملی/ ساعت) </th>
                            <th>پیش بینی مدت زمان تولید
                                <br/>
                                (تئوری/ ساعت) </th>
                            <th>  پیش بینی تاریخ شروع تولید
                                <br/>
                                (عملی)
                            </th>
                            <th>  پیش بینی تاریخ شروع تولید
                                <br/>
                                (تئوری) </th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($reserve_allocation_list as $item)
                            <tr>
                                <td>{{$item->allocation->priority_number}}</td>
                                <td>

                                    <a href="{{route("warps.production_card.view_card",$item->production)}}">{{$item->production->serial()}}
                                        @if($item->production->production_type_id ==2)
                                            <i class="fa fa-vial text-dark"></i>
                                        @endif
                                    </a>
                                    </a>

                                </td>
                                <td>
                                    {{$item->product->fullCaption()}}
                                </td>
                                <td>
                                    {{$item->production->get_allocation_amount($machine->id)}} {{$item->product->unit->caption??""}}
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
                                    <a  href="{{route("warps.$module_route_name.machine.allocation_card.index",[$machine,$item->allocation_id])}}">
                                        <i class="fa fa-print"></i>
                                    </a>

                                </th>


                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>

@endif
