@if(isset($machine_allocation_consumption["allocation"]))
    @foreach($machine_allocation_consumption["material"] as $machine_allocation_material)

        @foreach($machine_allocation_material as $product_id=>$current_machine_input_list)
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header">
                        <h5> مواد اولیه مصرفی
                            برای {{$machine_allocation_consumption["product"][$product_id]->caption}}</h5>
                    </div>
                    <div class="card-block">

                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>مواد اولیه</th>
                                    <th>مقدار پیش بینی</th>
                                    <th>مقدار مصرف واقعی</th>
                                    @if($show_actual_cost)
                                        <th>هزینه  (ریال)</th>
                                    @endif
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1;@endphp
                                @foreach($current_machine_input_list as $item)
                                    <tr>
                                        <td>{{$row++}}</td>

                                        <td>{{$item->material->caption}}</td>
                                        <td>{{round($item->predictive_amount,4)}}</td>
                                        @if(isset($machine_allocation_consumption["allocation"][$item->allocation_id ] [ $item->product_id ][$item->material_id]))
                                            <td>{{$machine_allocation_consumption["allocation"][$item->allocation_id ] [ $item->product_id ][$item->material_id]->actual_amount}}</td>
                                            @if($show_actual_cost)
                                                <td>{{$machine_allocation_consumption["allocation"][$item->allocation_id ] [ $item->product_id ][$item->material_id]->cost_of_one_unit}}</td>
                                            @endif
                                        @else

                                            <td title="محاسبه نشده / غیر قابل محاسبه"> ***</td>
                                            @if($show_actual_cost)
                                                <td></td>
                                            @endif
                                        @endif

                                    </tr>

                                @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
@endif

