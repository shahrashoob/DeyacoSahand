<div class="table-responsive">
    <table class="table table-styling center" style="">
        <thead>
        <tr>
            <th>ردیف</th>
            <th>شماره سفارش</th>
            <th>نام و کد کالا</th>
            <th> واحد سنجش</th>
            <th> مقدار درخواست</th>
            <th> مقدار مجوز</th>
            <th> مقدار تحویل شده</th>
            <th> مقدار باقی مانده</th>
            <th> موجودی فعال</th>
            <th> موجودی سایر بسته بندی ها </th>
            <th> موجودی بسته بندی های مجاز در راه </th>
            <th> موجودی سایر بسته بندی های  در راه </th>
            <th>نوع بسته بندی</th>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($product_request_form_items as $item)

            <tr>
                <td>{{++$row}}</td>
                <td>{{$item->order_series."/".$item->order_code}}</td>

                <td>{{$item->product->caption}} <br/>
                    {{$item->product->code}} <br/>
                    {{$item->product->id}}
                </td>
                <td>{{$item->product->unit->caption}}</td>
                <td>{{is_null($item->order_amount)?"":$item->order_amount}}</td>
                <td>{{is_null($item->amount_request)?"":$item->amount_request}}</td>

                <td>
                    {{round($item->amount_sent,4)}}

                </td>
                <td>{{is_null($item->amount_remaining)?"":$item->amount_remaining}}</td>
                <td>
                    {{$inventory_list[$item->product_id]}}
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td>
{{--                    @if($item->product_request_form_packing_types()->distinct("packing_type_id")->count()!=1)--}}
{{--                        <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown"--}}
{{--                                aria-haspopup="true" style="width: 140px" aria-expanded="false">--}}
{{--                            {{$item->product_request_form_packing_types()->distinct("packing_type_id")->count()}}--}}
{{--                            بسته بندی مجاز--}}
{{--                        </button>--}}
{{--                        <div class="dropdown-menu" style="text-align: center">--}}

{{--                            @foreach($item->product_request_form_packing_types()->groupby("packing_type_id")->get() as $item_p)--}}
{{--                                <a href="#12" class="dropdown-item"--}}
{{--                                   id="btn_confirm_print4">{{$item_p->packing_type->code}}--}}
{{--                                    - {{$item_p->packing_type->caption}}</a>--}}
{{--                            @endforeach--}}


{{--                        </div>--}}
{{--                    @else--}}
{{--                        {{$item->product_request_form_packing_types()->groupby("packing_type_id")->first()->packing_type->caption}}--}}
{{--                    @endif--}}

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>

<div class="float-left">
    نمايش رکوردهای
    <b>{{$product_request_form_items->firstItem()}}</b>
    تا
    <b>{{$product_request_form_items->lastItem()}}</b>
    از
    <b>{{$product_request_form_items->total()}}</b>
    رکورد موجود


</div>
<div class="text-center">
    {{$product_request_form_items->links('pagination::bootstrap-4')}}
</div>
