

@if($add_pricing)

        @endif
        <div class="row"  style="overflow: auto">
            <div class="col-md-12">

                <br/>


                @php $row=$product_tariffs->firstItem();@endphp
                <div style="overflow: auto">
                    <table class="table col-md-12 center">
                        <tr>
                            <th>ردیف</th>
                            <th>تعرفه</th>
                            <th>کد کالا</th>
                            <th> نام کالا</th>
                            <th>درجه</th>
                            <th>نوع بسته بندی</th>
                            <th>انبار</th>
                            <th>نوع فروش</th>
                            <th>حداقل خرید ({{$product->unit->caption}})</th>
                            <th>حداکثر خرید ({{$product->unit->caption}})</th>
                            <th>قیمت واحد (ریال)</th>
                            <th>قیمت مصرف کننده</th>
                            <th>به قیمت واحد فاکتور با <br/>توجه به راس پرداخت،<br/> n درصد به ازای <br/>هر روز اضافه شود</th>

                            <th>درصد عوارض</th>
                            <th>درصد ارزش افزوده</th>
                            <th></th>

                        </tr>
                        @foreach($product_tariff_pricing as $item)
                            <tr class="alert-warning">
                                <td>{{$row++}}</td>
                                <td>
                                    {{$item->tariff->caption}}
                                </td>
                                <td>
                                    {{$item->product->code}}
                                </td>
                                <td>
                                    {{$item->product->caption}}
                                </td>
                                <td>
                                    {{$item->degree->caption}}
                                </td>
                                <td>
                                    {{$item->packing_type->caption}}
                                </td>
                                <td>
                                    {{$item->warehouse->caption}}
                                </td>
                                <td>
                                    {{$item->type_of_sale_of_product->caption}}
                                </td>

                                <td>
                                    {{number_format($item->min_buy)}}
                                </td>
                                <td>
                                    {{number_format($item->max_buy)}}
                                </td>

                                    <td></td>
                                    <td></td>
                                <td>
                                    {{$item->increase_percentage_deadline_per_day}}
                                </td>
                                <td>
                                    {{$item->tax}}
                                </td>
                                <td>
                                    {{$item->fare}}
                                </td>


                            </tr>
                        @endforeach
                        @foreach($product_tariffs as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    {{$item->tariff->caption}}
                                </td>
                                <td>
                                    {{$item->product->code}}
                                </td>
                                <td>
                                    {{$item->product->caption}}
                                </td>
                                <td>
                                    {{$item->degree->caption}}
                                </td>
                                <td>
                                    {{$item->packing_type->caption}}
                                </td>
                                <td>
                                    {{$item->warehouse->caption}}
                                </td>
                                <td>
                                    {{$item->type_of_sale_of_product->caption}}
                                </td>
                                <td>
                                    {{number_format($item->min_buy)}}
                                </td>
                                <td>
                                    {{number_format($item->max_buy)}}
                                </td>
                                <td>
                                    {{number_format($item->fea)}}
                                </td>
                                <td>
                                    {{$item->consumer_price?number_format($item->consumer_price):""}}
                                </td>
                                <td>
                                    {{$item->increase_percentage_deadline_per_day}}
                                </td>
                                <td>
                                    {{$item->tax}}
                                </td>
                                <td>
                                    {{$item->fare}}
                                </td>


                            </tr>
                        @endforeach

                    </table>
                </div>

{{--                <div class="float-left">--}}
{{--                    نمايش رکوردهای--}}
{{--                    <b>{{$product_tariffs->firstItem()}}</b>--}}
{{--                    تا--}}
{{--                    <b>{{$product_tariffs->lastItem()}}</b>--}}
{{--                    از--}}
{{--                    <b>{{$product_tariffs->total()}}</b>--}}
{{--                    رکورد موجود--}}


{{--                </div>--}}
            </div>


        </div>
        <div class="text-center">
            {{$product_tariffs->links('pagination::bootstrap-4')}}
        </div>
@include($view_path."_btn_list")