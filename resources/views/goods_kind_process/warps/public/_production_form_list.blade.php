@if(count($production->production_form_item) > 0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> لیست فرم های تولید شده</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شماره ردیف فرم تولید</th>
                            <th>مقدار پیش بینی تولید</th>
                            <th>مقدار نهایی فرم تولید</th>
                            <th> وضعیت فرم تولید</th>
                            <th>شماره ردیف  بسته بندی</th>
                            <th>وضعیت فرم  بسته بندی</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @foreach($production->production_form_item as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    <a href="{{route("warps.production_form.view",$item->production_form_id)}}">

                                    {{$item->getCode()}}
                                    </a>
                                </td>
                                <td>{{$item->forecast_amount}}</td>
                                <td>{{$item->final_amount}}</td>
                                <td>{{$item->production_form->status->caption??"*"}}</td>
                                <td>
                                    @php $packing_form_item=$item->getPackingFormItem();@endphp
                                    @if($packing_form_item)
                                        <a href="{{route("fabric_raw.packing_form.view",$packing_form_item->packing_form_id)}}">
                                            {{$packing_form_item->code??""}}
                                        </a>

                                    @endif
                                </td>
                                <td>
                                    @if($packing_form_item)
                                            {{$packing_form_item->packing_form->status->caption??""}}
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

