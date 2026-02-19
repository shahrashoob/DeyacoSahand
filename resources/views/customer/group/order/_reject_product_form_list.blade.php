@if(isset($reject_product_form_list) && count($reject_product_form_list)>0)
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5> لیست فرم های مرجوعی</h5>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شماره فرم مرجوعی</th>
                            <th>شماره برگ خروج</th>
                            <th>تاریخ ایجاد</th>
                            <th>تعداد بسته بندی</th>
                            <th>وضعیت</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $i=1;@endphp
                        @foreach($reject_product_form_list as $item)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>
                                    <a href="{{route($route_path,[$order,$item->id])}}">
                                        {{$item->getCode()}}
                                    </a>
                                </td>
                                <td>
                                    {{$item->exit_form->code}}

                                </td>
                                <td>{{$item->get_create_date_and_time()}}</td>
                                <td>{{$item->items()->count()}}</td>
                                <td>

                                    {{$item->status->caption}}

                                </td>
                                <td>
                                    @if($item->status_id == 7009007 && isset($customer_reject))
                                        <a href="{{route("customer_group.order.view_reject_product_form",[$order,$item->id])}}">
                                            ثبت ارسال کالا
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
