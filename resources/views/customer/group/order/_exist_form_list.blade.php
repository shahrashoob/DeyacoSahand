@if(isset($form_list) && count($form_list)>0)
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5> لیست برگ (های) خروج از انبار</h5>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شماره فرم درخواست کالا</th>
                            <th>شماره برگ خروج</th>
                            <th>تاریخ ارسال</th>
                            <th>تعداد بسته بندی</th>
                            <th>وضعیت برگ خروج</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $i=1;@endphp
                        @foreach($form_list as $item)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>
                                    <a href="{{route("customer_group.order.view_product_request_form",[$order,$item->product_request_form_id??0])}}">
                                        {{$item->product_request_form_code}}
                                    </a>
                                </td>
                                <td>

                                    <a href="{{route("customer_group.order.view_form",[$order,$item->id])}}">
                                       دریافت فایل
                                    </a>

                                </td>
                                <td>{{$item->get_create_date_and_time()}}</td>
                                <td>{{count($item->getPackingFormList()->toArray())}}</td>
                                <td>
                                    @if($item->status_id ==500000500  )
                                        {{--   در انتظار تایید در خواست کننده--}}
                                        <a class=""
                                           href="{{route("customer_group.confirmation_of_receipt_of_product.index",[$order,$item->id])}}">
                                            تایید دریافت کالا
                                        </a>
                                    @else
                                        {{$item->status->caption}}
                                    @endif
                                </td>
                                <th>
                                    <a class="" href="{{route("customer_group.order.download_form",[$order,$item->id,3,"product"])}}">
                                        <i class="fa fa-download"></i>
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
