@php $orderList=$order->orderList()->get();@endphp

@if(count($orderList)>0)
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5> مشخصات پردازش سفارش</h5>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-striped table-hover "
                           style="text-align: center;font-size:11px;width: 100%">
                        <thead>
                        <tr>
                            <th> ردیف</th>
                            <th>کد کالا</th>
                            <th>نام کالا</th>
                            <th>درجه</th>
                            <th>بسته بندی</th>
                            <th>مقدار</th>
                            <th>واحد سنجش</th>
                            <th>بسته بندی های ارسال شده</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;
                        @endphp
                        @foreach($order->orderFactor as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>{{$item->product->code}}</td>
                                <td>{{$item->product->caption}}</td>
                                <td>{{$item->degree->caption}}</td>
                                <td>
                                 <span title="{!!  $item->getPackingType("tooltip") !!}">
                                    {{$item->getPackingType()}}
                                    </span>
                                </td>
                                <td>{{$item->carton}}</td>
                                <td>{{$item->product->unit->bach_caption}}</td>
                                <th>

                                </th>
                                <td>
                                    @if($order->status_id == 304080)
                                        <a href="{{route("sales.production_processing.index",$item->order_list_id)}}">
                                            پردازش سطر
                                        </a>
                                    @else
                                        <a href="{{route("sales.production_processing.index",$item->order_list_id)}}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @endif

                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
