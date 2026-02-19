@if(count($packing_form->items)>0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5>لیست آیتم های موجود</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive center">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>ردیف</th>
                            <th>شماره فرم تولید</th>
                            <th>سریال تولید</th>
                            <th>وضعیت سریال تولید</th>
                            <th>سریال سطح بالا</th>
                            <th>وضعیت سریال سطح بالا</th>
                            <th>کد کالا</th>
                            <th>نام کالا</th>
                            <th>{{$packing_form->getUnitCaption("unit","measurement")}}</th>
                            <th>{{$packing_form->getUnitCaption("sub_unit","measurement")}}</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @foreach($packing_form->items as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>{{$item->getCode(false,false,$show_packing_code??1)}}</td>
                                <td>{{$item->production_form_item->code??""}}</td>
                                <td>{{$item->production_form_item->production->serial??""}}</td>
                                <td>{{$item->production_form_item->production->waiting_status->caption??""}}</td>
                                <td>{{$item->production_form_item->production->parent_production->serial??""}}</td>
                                <td>{{$item->production_form_item->production->parent_production->waiting_status->caption??""}}</td>
                                <td>{{$item->product->code}}</td>
                                <td>{{$item->product->caption}}</td>
                                <td>{{$item->final_amount}}</td>
                                <td>{{$item->sub_amount}}</td>
                        @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
@endif

@if(count($packing_form->sub_packing)>0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5>لیست بسته بندی ها</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <td>ردیف</td>
                            <th>کد بسته بندی</th>
                            <th> نوع بسته بندی </th>
                            <td>کد کالا</td>
                            <td>نام کالا</td>
                            <th>{{$packing_form->getUnitCaption("unit","measurement")}}</th>
                            <th>{{$packing_form->getUnitCaption("sub_unit","measurement")}}</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @foreach($packing_form->sub_packing as $sub_packing)
                            @foreach($sub_packing->packing_form->items as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>{{$item->getCode(false,false,$show_packing_code??1)}}</td>
                                    <td>{{$sub_packing->packing_form->code}}</td>
                                    <td>{{$sub_packing->packing_form->packing_type->caption}}</td>
                                    <td>{{$item->product->code}}</td>
                                    <td>{{$item->product->caption}}</td>
                                    <td>{{$item->final_amount}}</td>
                                    <td>{{$item->sub_amount}}</td>
                            @endforeach
                        @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
@endif
