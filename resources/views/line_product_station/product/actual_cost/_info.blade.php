<div class="row">


    @foreach($product->type_of_sale_of_products as $type_of_sale_of_product)
            {{$type_of_sale_of_product->type_of_sale_of_product_id}}
        @switch($type_of_sale_of_product->type_of_sale_of_product_id)
            {{--                فروش عادی--}}
            @case(1)
                <div class="col-md-12">
                    <h5>
                        بهای تمام شده به ازای هر بسته بندی از کالای
                        <b> {{$product->fullCaption()}}</b>
                    </h5>
                    <br/>
                    @php $row=1;@endphp
                    <table class="table col-md-6 center">
                        <tr>
                            <th></th>
                            <th>کد نوع بسته بندی</th>
                            <th>عنوان نوع بسته بندی</th>
                            <th>براساس میانگین قیمت در دوره مالی (ریال)</th>
                            <th>براساس آخرین قیمت (ریال)</th>
                            <th>براساس قیمت روز (ریال)</th>

                        </tr>
                        @foreach($product->packing_types as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    <b> {{$item->code}} </b>
                                </td>
                                <td>
                                    <b> {{$item->caption}} </b>
                                </td>
                                @if($packing_form_actual_cost)
                                    <td>{{$packing_form_actual_cost->costBaseOnAverageLatestPrice("number_format",1000,$item->id)}}</td>
                                    <td>{{$packing_form_actual_cost->costBaseOnLatestPrice("number_format",1000,$item->id)}}</td>

                                @else
                                    <td></td>
                                    <td></td>
                                @endif
                                <td>
                                    {!! $product->costBaseOnCurrentDay($item->id,"number_format_with_logs") !!}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                @break

                {{--                فروش کارمزدی--}}
            @case(2)
                <div class="col-md-12">
                    <h5>
                        بهای تمام شده خدمت
                        <b> {{$type_of_sale_of_product->service->fullCaption()}}</b>
                    </h5>
                    <br/>
                    <table class="table col-md-6 center">
                        <tr>
                            <th>براساس میانگین قیمت در دوره مالی (ریال)</th>
                            <th>براساس آخرین قیمت (ریال)</th>
                            <th>براساس قیمت روز (ریال)</th>

                        </tr>
                        <tr>
                            @if($packing_form_actual_cost)
                                <td></td>
                                <td></td>
                                {{--                                <td>{{$packing_form_actual_cost->costBaseOnAverageLatestPrice("number_format",1000,$item->id)}}</td>--}}
                                {{--                                <td>{{$packing_form_actual_cost->costBaseOnLatestPrice("number_format",1000,$item->id)}}</td>--}}
                            @else
                                <td></td>
                                <td></td>
                            @endif
                            <td>
{{--                                {!! $product->costBaseOnCurrentDay($item->id,"number_format_with_logs") !!}--}}
                            </td>
                        </tr>
                    </table>
                </div>
                @break
        @endswitch

    @endforeach

        <div class="col-md-12">
            @include($view_path."_btn_list")
        </div>
</div>


