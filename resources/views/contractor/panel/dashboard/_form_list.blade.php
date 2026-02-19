@if(count($product_request_form_list)>0)

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> لیست فرم های تحویل مواد اولیه </h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling center" style="">
                        <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>شماره فرم درخواست کالا</th>
                            <th>شماره فرم ارسال کالا</th>
                            <th>تاریخ ارسال</th>
                            <th> تعداد بسته</th>
                            <th>وضعیت</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($product_request_form_list as $product_form)
                            @foreach($product_form->forms()->groupBy("form_id")->get() as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("contractor.panel.dashboard.view_product_request_form",[$contractor_allocation,$product_form])}}">
                                            {{$product_form->getCode()}}
                                        </a>

                                    </td>
                                    <td>
                                        <a href="{{route("contractor.panel.dashboard.view_form",[$contractor_allocation,$item->form])}}">
                                            {{$item->form->code}}
                                        </a>

                                    </td>
                                    <td>{{$item->form->get_create_date_and_time()}}</td>
                                    <td>{{count($item->form->getPackingFormList()->toArray())}}</td>
                                    <td>
                                        @if($item->form->status_id == 500000500)
                                            {{--   در انتظار تایید در خواست کننده--}}
                                            <a class="btn btn-success btn-sm"
                                               href="{{route("contractor.panel.confirmation_of_receipt_of_product.index",[$contractor_allocation,$item,$item->form])}}">
                                                تایید دریافت کالا
                                            </a>

                                        @else
                                            {{$item->form->status->caption}}
                                        @endif
                                    </td>
                                    <th>

                                    </th>
                                </tr>
                            @endforeach
                            @if(count($product_form->forms)==0)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("contractor.panel.dashboard.view_product_request_form",[$contractor_allocation,$product_form])}}">
                                            {{$product_form->getCode()}}
                                        </a>

                                    </td>
                                    <td>


                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>

                                    </td>
                                    <th>

                                    </th>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
