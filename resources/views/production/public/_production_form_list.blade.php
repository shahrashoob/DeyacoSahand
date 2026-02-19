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
                            <th>ماشین</th>
                            <th>مقدار پیش بینی تولید</th>
                            <th>مقدار سیستم فرم تولید</th>
                            <th>مقدار نهایی فرم تولید</th>
                            <th> وضعیت فرم تولید</th>
                            <th>   بسته بندی (ها)</th>
                            <th>وضعیت فرم  بسته بندی</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @foreach($production->production_form_item()->paginate() as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    <a href="{{route($goods_kind_caption.".production_form.view",$item->production_form_id)}}">

                                    {{$item->getCode()}}
                                    </a>
                                </td>
                                <td>{{$item->production_form->machine->caption}}</td>
                                <td>{{$item->forecast_amount}}</td>
                                <td>{{round($item->amount,2)}}</td>
                                <td>{{round($item->final_amount,2)}}</td>
                                <td>{{$item->production_form->status->caption??"*"}}</td>
                                <td>
                                    @php $packing_form_items_count=$item->getPackingFormItemsCount();@endphp
                                    @if($packing_form_items_count==1)
                                        @php $packing_form_item=$item->getPackingFormItem();@endphp
                                        <a href="{{route("fabric_raw.packing_form.view",$packing_form_item->packing_form_id??"")}}">
                                            {{$packing_form_item->code??""}}
                                        </a>
                                    @else

                                        {{$packing_form_items_count}}
                                        بسته

                                    @endif
                                </td>
                                <td>
                                    @if(isset($packing_form_item))
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

