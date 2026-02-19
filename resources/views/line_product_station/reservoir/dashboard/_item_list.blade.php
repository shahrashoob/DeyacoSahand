@if($packing_form && count($packing_form->items)>0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5>لیست کالاهای موجود</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive center">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>ردیف</th>
                            <th>کد کالا</th>
                            <th>نام کالا</th>
                            <th>درجه کالا</th>
                            <th>لات</th>
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

                                <td>{{$item->product->code}}</td>
                                <td>{{$item->product->caption}}</td>
                                <td>{{$item->degree->caption}}</td>
                                <td>{{$item->lot_number->code}}</td>
                                <td>{{round($item->final_amount,6)}}</td>
                                <td>
                                    @if($item->product->sub_unit)
                                        {{round($item->sub_amount,6)}}
                                    @endif
                                </td>
                        @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
@endif

