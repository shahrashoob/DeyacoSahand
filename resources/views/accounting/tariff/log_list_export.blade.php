<table>
    <thead>
    <tr>
        <th>tariff_id</th>
        <th>product_code</th>
        <th>product_caption</th>
        <th>degree_code</th>
        <th>degree_caption</th>
        <th>packing_type_code</th>
        <th>packing_type_caption</th>
        <th>warehouse_code</th>
        <th>warehouse_caption</th>
        <th>number_in_carton</th>
        <th>unit_caption</th>
        <th>fea</th>
        <th>min_buy</th>
        <th>max_buy</th>
        <th>tax</th>
        <th>fare</th>
        <th>consumer_price</th>
        <th>type_of_sale_of_product_id</th>
        <th>service_code</th>
        <th>service_caption</th>
        <th>increase_percentage_deadline_per_day</th>
        <th>customer_product_code</th>
        <th>customer_product_caption</th>
    </tr>
    <tr>
        <th>کد تعرفه</th>
        <th>کد کالا</th>
        <th>نام کالا</th>
        <th>کد درجه</th>
        <th>نام درجه</th>
        <th>کد نوع بسته بندی</th>
        <th>نام نوع بسته بندی</th>
        <th>کد انبار</th>
        <th>نام انبار</th>
        <th>تعداد در واحد اصلی</th>
        <th> واحد</th>
        <th>قیمت واحد</th>
        <th>حداقل خرید</th>
        <th> حداکثر خرید</th>
        <th>درصد مالیات</th>
        <th> درصد ارزش افزوده</th>
        <th> قیمت مصرف کننده</th>
        <th>نوع فروش ( 1: فروش عادی، 2: فروش کارمزدی)</th>
        <th>کد خدمت  کالا برای فروش کارمزدی</th>
        <th>نام خدمت  کالا برای فروش کارمزدی</th>
        <th>به قیمت واحد فاکتور با توجه به راس پرداخت، n درصد به ازای هر روز اضافه شود
        </th>
        <th>کد  کالای مشتری
        </th>
        <th>عنوان کالای مشتری
        </th>
    </tr>

    </thead>
    <tbody>
    @foreach($list as $item)
        <tr>

            <td>{{$item->tariff_id}}</td>
            <td>{{$item->product->code??""}}</td>
            <td>{{$item->product->caption??""}}</td>
            <td>{{$item->degree->code??""}}</td>
            <td>{{$item->degree->caption??""}}</td>
            <td>{{$item->packing_type->code??""}}</td>
            <td>{{$item->packing_type->caption??""}}</td>
            <td>{{$item->warehouse->code??""}}</td>
            <td>{{$item->warehouse->caption??""}}</td>
            <td>{{$item->product->number_in_carton}}</td>
            <td>{{$item->product->unit->caption}}</td>
            <td>{{$item->fea}}</td>
            <td>{{$item->min_buy}}</td>
            <td>{{$item->max_buy}}</td>
            <td>{{$item->tax}}</td>
            <td>{{$item->fare}}</td>
            <td>{{$item->consumer_price}}</td>
            <td>{{$item->type_of_sale_of_product_id}}</td>
            <td>{{$item->service->code??""}}</td>
            <td>{{$item->service->caption??""}}</td>
            <td>{{$item->increase_percentage_deadline_per_day}}</td>
            <td>{{$item->customer_product_code}}</td>
            <td>{{$item->customer_product_caption}}</td>

        </tr>
    @endforeach
    </tbody>

</table>
