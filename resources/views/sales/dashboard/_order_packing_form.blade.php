@if($order->order_packing_forms()->count() >0)
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>مشخصات بسته بندی های   مشتری</h5>
            </div>
            <div class="card-block">
                <div class="row">
                    <table class="table table-hover center">
                        <thead>
                        <tr>
                            <th class="center">ردیف</th>
                            <th>نام کالا</th>
                            <th>نام ماده اولیه</th>
                            <th>کد بسته بندی مشتری</th>
                            <th>کد بسته بندی</th>
                            <th>درجه</th>
                            <th>لات</th>
                            <th>مقدار</th>

                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @foreach($order->order_packing_forms as $order_packing_form)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>{{$order_packing_form->product->caption}}</td>
                                <td>{{$order_packing_form->material->caption}}</td>
                                <td>{{$order_packing_form->packing_form_code}}</td>
                                <td>{{isset($order_packing_form->packing_form)?$order_packing_form->packing_form->getCode():""}}</td>
                                <td>{{$order_packing_form->degree->caption}}</td>
                                <td>{{$order_packing_form->lot_number_code}}</td>
                                <td>{{$order_packing_form->amount}}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif