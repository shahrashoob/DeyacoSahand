@if(isset($allocation->items))

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>تخصیص  جاری - تخصیص شماره
                    {{$allocation->id}}
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
                            <th>مقدار تخصیص</th>
                            <th>باند</th>
                            <th>تاریخ رزرو</th>
{{--                            <th>پیش بینی مدت زمان تولید--}}
{{--                                <br/>--}}
{{--                                (عملی/ ساعت) </th>--}}
{{--                            <th>پیش بینی مدت زمان تولید--}}
{{--                                <br/>--}}
{{--                                (تئوری/ ساعت) </th>--}}
{{--                            <th>  پیش بینی تاریخ شروع تولید--}}
{{--                                <br/>--}}
{{--                                (عملی)--}}
{{--                            </th>--}}
{{--                            <th>  پیش بینی تاریخ شروع تولید--}}
{{--                                <br/>--}}
{{--                                (تئوری) </th>--}}
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0; $current_is_ok=0;@endphp
                        @foreach($allocation->items as $item)
                            <tr
                                    @if($item->status_id == 5310010 && $current_is_ok ==0)
                                        style="background: #a2c2a2"

                                    @php
                                        //عملیات های بچ همه ردیف ها باهم فعال می شوند.
                                        if($item->line_product_station->station_operation->station_operation_type_id ==2) { $current_is_ok=1;  } @endphp
                                    @endif

                                    @if($item->status_id == 5310020 )
                                        style="background: rgba(210,201,201,0.68)"
                                    @endif
                                    @if($item->status_id == 5310060 )
                                        style="background: rgba(206,206,151,0.68)"
                                    @endif
                            >

                                <td>{{++$row}}</td>
                                <td>

                                    <a href="{{route("fabric.production_card.view_card",$item->production_id)}}">{{$item->production->serial()}}
                                        @if($item->production->production_type_id ==2)
                                            <i class="fa fa-vial text-dark"></i>
                                        @endif
                                    </a>

                                </td>
                                <td>
                                    {{$item->product->fullCaption()}}
                                </td>
                                <td>
                                    {{$item->production->get_allocation_amount($machine->id, 1,$item->allocation_id,false,$allocation->allocation_unit_type_id)}} {{$allocation->allocation_unit_type($item->product)}}
                                </td>
                                <td>
                                    {{$item->band_code}}
                                </td>
                                <td>
                                    {{$item->get_datetime()}}
                                </td>
{{--                                <td>--}}
{{--                                    {{$item->allocation->partical_houre()}}--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    {{$item->allocation->theory_houre()}}--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    {{$item->allocation->get_partical_datetime()}}--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    {{$item->allocation->get_theory_datetime()}}--}}
{{--                                </td>--}}
                                <th>


{{--                                    <a href="" class="text-danger">--}}
{{--                                        <i class="fas fa-external-link-alt" style="transform: rotate(225deg);"></i>--}}
{{--                                        استخراج از ماشین </a>--}}

{{--                                    <a  href="{{route("fabric.special_production.machine.allocation_card.index",[$machine,$item->allocation_id])}}">--}}
{{--                                        <i class="fa fa-print"> </i>پرینت --}}
{{--                                    </a>--}}

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