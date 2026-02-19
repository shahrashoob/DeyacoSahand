@if(count($quality_control_product_faults)> 0)

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5>نقص های کالا برای بسته بندی</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th></th>
                            <th>کد بسته بندی</th>
                            <th>تاریخ کنترل کیفی</th>
                            <th>نام کالا</th>
                            <th>کد کالا</th>
                            <th>باند</th>
                            <th>شروع</th>
                            <th>پایان</th>
                            <th>نوع نقص</th>
                            <th>اپراتور</th>
                            <th>ماشین</th>
                            <th>آیا نقص برطرف شده است</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=$quality_control_product_faults->firstItem();@endphp
                        @foreach($quality_control_product_faults as $quality_control_product_fault)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    <a target="_blank"
                                       href="{{route("fabric_raw.packing_form.view",$quality_control_product_fault->packing_form_id)}}}}">
                                        {{$quality_control_product_fault->packing_form->code}}
                                    </a>

                                </td>
                                <td>
                                    {{$quality_control_product_fault->create_datetime()}}
                                </td>
                                <td>{{$quality_control_product_fault->product->caption??""}}</td>
                                <td>{{$quality_control_product_fault->product->code??""}}</td>
                                <td>{{$quality_control_product_fault->band_code}}</td>
                                <td>{{$quality_control_product_fault->start_point}} {{$quality_control_product_fault->point}}</td>
                                <td>{{$quality_control_product_fault->end_point}} </td>
                                <td>{{$quality_control_product_fault->product_fault->caption??""}}</td>
                                <td>{{$quality_control_product_fault->get_operator_fullname()}}</td>
                                <td>{{$quality_control_product_fault->production_form->machine->caption??""}}</td>
                                <td>{{$quality_control_product_fault->fault_is_fixed?"بله":"خیر"}}</td>
                                <td>
                                    @foreach($quality_control_product_fault->product_fault_propery_values as $product_fault_property)

                                        {{$product_fault_property->product_fault_property->caption??""}}:
                                        {{$product_fault_property->getValue()}}

                                        &nbsp;
                                        &nbsp;

                                    @endforeach

                                </td>

                            </tr>
                        @endforeach


                        </tbody>

                    </table>
                </div>
                <div class="float-left">
                    نمايش رکوردهای
                    <b>{{$quality_control_product_faults->firstItem()}}</b>
                    تا
                    <b>{{$quality_control_product_faults->lastItem()}}</b>
                    از
                    <b>{{$quality_control_product_faults->total()}}</b>
                    رکورد موجود
                </div>

            </div>
            <div class="text-center">
                {{$quality_control_product_faults->links('pagination::bootstrap-4')}}
            </div>
        </div>

    </div>

@endif