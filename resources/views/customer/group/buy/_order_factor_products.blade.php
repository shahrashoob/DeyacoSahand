@if(!isset($type_show))

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>مشخصات کالا / خدمات مورد معامله </h5>
            </div>
            <div class="card-block overflow-auto">
                <table class="table table-striped table-hover " style="text-align: center;font-size:11px;width: 100%">
                    <thead>
                    <tr>
                        <th> ردیف</th>
                        <th>کد کالا / خدمت</th>
                        <th>نام کالا / خدمت</th>
                        <th>درجه</th>
                        <th>بسته بندی</th>
                        <th>مقدار</th>
                        <th>واحد سنجش</th>
                        <th>مبلغ واحد</th>
                        <th>مبلغ کل</th>
                        <th>مبلغ تخفیف</th>
                        <th>بها پس از تخفیف</th>
                        <th>جمع مالیات و عوارض</th>
                        <th>جمع مبلغ سطر</th>
                        @if(isset($allow_delete_rows))
                            <th></th>
                        @endif
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=1;
                             $sum_price=0;
                             $sum_total_price=0;
                             $sum_off_price=0;
                             $sum_tax_price=0;
                             $sum_total_price_with_tax=0;
                    @endphp
                    @foreach($order->orderFactor()->join("products","products.id","product_id")->orderBy("property1_caption")->select("order_factor.*")->get() as $item)
                        @php
                            $sum_price+=$item->price;
                            $sum_total_price+=$item->total_price;
                            $sum_off_price+=$item->total_off_price;
                            $sum_tax_price+=$item->tax_price;
                            $sum_total_price_with_tax+=$item->total_price_with_tax;
                        @endphp
                        <tr>
                            <td>{{$row++}}</td>
                            <td>
                                @if($item->service_id)
                                    {{$item->service->code}}
                                @else
                                    {{$item->product->code}}
                                @endif
                            </td>
                            <td>
{{--                                {{$item->product->property1_caption}}--}}
                                @if(isset($allow_delete_rows))
                                    <a href="{{route("customer_group.buy.show_shopping_product",[$order,$order->customer,$item->product_id])}}">
                                        {{$item->getCaption()}}
                                    </a>
                                @else
                                    {{$item->getCaption()}}
                                @endif
                            </td>
                            <td>{{$item->degree->caption}}</td>
                            <td>
                            <span title="{!!  $item->getPackingType("tooltip") !!}">
                            {{$item->getPackingType()}}
                            </span>
                            </td>
                            <td>{{$item->carton}}</td>
                            <td>{{$item->product->unit->bach_caption}}</td>
                            <td>@to_money($item->fea)</td>
                            <td>@to_money($item->price)</td>
                            <td>@to_money($item->total_off_price)</td>
                            <td>@to_money($item->total_price)</td>
                            <td>@to_money($item->tax_price)</td>
                            <td>@to_money($item->total_price_with_tax)</td>
                            @if(isset($allow_delete_rows))
                                <td>
                                    <a href="{{route("customer_group.buy.remove_from_shopping_cart",[$order,$order->customer,$item->product_id])}}">
                                        <i class="fa fa-trash text-danger"></i>
                                    </a>
                                </td>
                            @endif

                        </tr>
                    @endforeach
                    <tr style="font-size: 12px; font-weight: bold">
                        <td colspan="5" style="text-align: right">

                        </td>
                        <td colspan="3"> جمع کل ({{$order->customer->parent->tariff->currency->caption??""}})</td>
                        <td>@to_money(round($sum_price))</td>
                        <td>@to_money(round($sum_off_price))</td>
                        <td>@to_money(round($sum_total_price))</td>
                        <td>@to_money(round($sum_tax_price))</td>
                        <td>@to_money(round($sum_total_price_with_tax))</td>
                        @if(isset($allow_delete_rows))
                            <td></td>
                        @endif
                    </tr>
                    </tbody>
                </table>


                <form id="form2" style="display: inline"
                      action="{{route("customer_group.buy.change_selling_type",$order)}}" method="post"
                      autocomplete="off">
                    @csrf
                    {{--                نوع پیش فاکتور:--}}

                    {{--                    {{$order->selling_type->caption??""}}--}}
                </form>


            </div>
        </div>
    </div>

@elseif($type_show == "minimal")

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>سبد خرید </h5>
            </div>
            <div class="card-block overflow-auto">
                <table class="table table-striped table-hover " style="text-align: center;font-size:11px;width: 100%">
                    <thead>
                    <tr>
                        <th> ردیف</th>
                        <th>نام کالا</th>
                        <th>مقدار</th>
                        <th>مبلغ واحد</th>
                        <th>مبلغ کل</th>
                        <th>مبلغ تخفیف</th>
                        <th>بها پس از تخفیف</th>
                        <th> مالیات</th>
                        <th>قیمت کل</th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=1;
                             $sum_price=0;
                             $sum_total_price=0;
                             $sum_off_price=0;
                             $sum_tax_price=0;
                             $sum_total_price_with_tax=0;
                    @endphp
                    @foreach($order->orderFactor as $item)
                        @php
                            $sum_price+=$item->price;
                            $sum_total_price+=$item->total_price;
                            $sum_off_price+=$item->total_off_price;
                            $sum_tax_price+=$item->tax_price;
                            $sum_total_price_with_tax+=$item->total_price_with_tax;
                        @endphp
                        <tr>
                            <td>{{$row++}}</td>
                            <td>
                                {{$item->getCaption()}}
                                <br/>
                                @if($item->service_id)
                                    {{$item->service->code}}
                                @else
                                    {{$item->product->code}}
                                @endif


                            </td>

                            <td>{{$item->carton}}
                                {{$item->product->unit->bach_caption}}</td>
                            <td>@to_money($item->fea)</td>
                            <td>@to_money($item->price)</td>
                            <td>@to_money($item->total_off_price)</td>
                            <td>@to_money($item->total_price)</td>
                            <td>@to_money($item->tax_price)</td>
                            <td>@to_money($item->total_price_with_tax)</td>
                        </tr>
                    @endforeach
                    <tr style="font-size: 12px; font-weight: bold">

                        <td colspan="4"> جمع کل ({{$order->customer->parent->tariff->currency->caption??""}})</td>
                        <td>@to_money(round($sum_price))</td>
                        <td>@to_money(round($sum_off_price))</td>
                        <td>@to_money(round($sum_total_price))</td>
                        <td>@to_money(round($sum_tax_price))</td>
                        <td>@to_money(round($sum_total_price_with_tax))</td>
                    </tr>
                    </tbody>
                </table>


                {{--                <form id="form2" style="display: inline"--}}
                {{--                      action="{{route("customer_group.buy.change_selling_type",$order)}}" method="post"--}}
                {{--                      autocomplete="off">--}}
                {{--                    @csrf--}}
                {{--                    --}}{{--                نوع پیش فاکتور:--}}

                {{--                    --}}{{--                    {{$order->selling_type->caption??""}}--}}
                {{--                </form>--}}


            </div>
        </div>
    </div>

@endif
