@foreach($packing_form->items as $packing_form_item)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5>بهای تمام شده واحد کالا ({{$packing_form_item->product->fullCaption()}})</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling center">
                        <thead>
                        <tr>

                            <th></th>
                            <th>براساس قیمت محموله (ریال)</th>
                            <th>براساس میانگین قیمت در دوره مالی (ریال)</th>
                            <th>براساس آخرین قیمت (ریال)</th>
                            <th>براساس قیمت روز (ریال)</th>
                        </tr>

                        </thead>
                        <tbody>

                        @foreach($packing_form_actual_costs->where("product_id",$packing_form_item->product_id) as $packing_form_actual_cost)
                            <tr>
                                <th>{{$packing_form_actual_cost->actual_cost_type->caption}}</th>
                                <td>{{$packing_form_actual_cost->get_cost_of_one_unit("number_format")}}</td>
                                @if($packing_form_actual_cost->actual_cost_type_id ==1000)
                                    <td>{{$packing_form_actual_cost->costBaseOnAverageLatestPrice("number_format")}}</td>
                                    <td>{{$packing_form_actual_cost->costBaseOnLatestPrice("number_format")}}</td>
                                    <td>{{$packing_form_actual_cost->costBaseOnCurrentDay("number_format")}}</td>
                                @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                @endif


                            </tr>
                            @endforeach


                            {{--                            @if($packing_form_item->machine_allocation_actual_cost)--}}
                            {{--                                <th>بهای تمام شده</th>--}}
                            {{--                                <td>{{$packing_form_item->machine_allocation_actual_cost->costOfOneUnit("number_format")}}</td>--}}

                            {{--                                <td>{{$packing_form_item->machine_allocation_actual_cost->costBaseOnAverageLatestPrice($packing_form->packing_type_id,"number_format")}}</td>--}}
                            {{--                                <td>{{$packing_form_item->machine_allocation_actual_cost->costBaseOnLatestPrice($packing_form->packing_type_id,"number_format")}}</td>--}}
                            {{--                                <td>{{$packing_form_item->machine_allocation_actual_cost->costBaseOnCurrentDay($packing_form->packing_type_id,"number_format")}}</td>--}}
                            {{--                            @else--}}
                            {{--                                <th>بهای تمام شده</th>--}}
                            {{--                                <td></td>--}}
                            {{--                                <td></td>--}}
                            {{--                                <td></td>--}}
                            {{--                                <td></td>--}}
                            {{--                            @endif--}}


                            </tr>


                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>
@endforeach