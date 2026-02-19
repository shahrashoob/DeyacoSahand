<div class="row">
    @foreach($product->type_of_sale_of_products as $type_of_sale_of_product)

        @switch($type_of_sale_of_product->type_of_sale_of_product_id)
            {{--                فروش عادی--}}
            @case(1)
                <div class="col-md-12">

                    <h5>
                        پیش بینی بهای تمام شده به ازای هر بسته بندی از کالای
                        <b> {{$product->fullCaption()}}</b>
                    </h5>
                    <br/>
                    @php $row=1;@endphp
                    <table class="table col-md-6 center" style="max-width: 100%; overflow: auto">
                        <tr>
                            <th></th>
                            <th>کد نوع بسته بندی</th>
                            <th>عنوان نوع بسته بندی</th>
                            @if(isset($only_current_price))
                                @php global $all_pricing_claculated;@endphp

                                <th>براساس قیمت روز (ریال)</th>
                            @else
                                <th>براساس میانگین قیمت در دوره مالی (ریال)</th>
                                <th>براساس آخرین قیمت (ریال)</th>
                                <th>براساس قیمت روز (ریال)</th>
                            @endif

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
                                @if(isset($only_current_price))
                                    <td>

                                        @php
                                            $result=$product->costBaseOnCurrentDayPredict("number_format","result") ;
                                            // همه ردیف های قیمت گذاری باید مقدار داشته باشند و بدون خطا
                                            $all_pricing_claculated=$all_pricing_claculated&$result["result"];
                                        @endphp
                                        {!! $result["result"]?$result["html_tree"]:$result["error"] !!}
                                    </td>
                                @else
                                    <td>{!! $product->costBaseOnAverageLatestPricePredict("number_format") !!}</td>
                                    <td>{!! $product->costBaseOnLatestPricePredict("number_format") !!}</td>

                                    <td>
                                        @php
                                            $result=$product->costBaseOnCurrentDayPredict("number_format","result") ;
                                        @endphp
                                        {!! $result["result"]?$result["html_tree"]:$result["error"] !!}

                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>


                </div>
                @break

                {{--                فروش کارمزدی--}}
            @case(2)
                <div class="col-md-12">

                    <h5>
                        پیش بینی بهای تمام شده برای خدمت
                        <b> {{$type_of_sale_of_product->service->fullCaption()}}</b>
                    </h5>
                    <br/>

                    <table class="table col-md-6 center" style="max-width: 100%; overflow: auto">
                        <tr>
                            @if(isset($only_current_price))
                                @php global $all_pricing_claculated;@endphp

                                <th>براساس قیمت روز (ریال)</th>
                            @else
                                <th>براساس میانگین قیمت در دوره مالی (ریال)</th>
                                <th>براساس آخرین قیمت (ریال)</th>
                                <th>براساس قیمت روز (ریال)</th>
                            @endif

                        </tr>
                        @foreach($product->packing_types as $item)
                            <tr>
                                @if(isset($only_current_price))
                                    <td>

                                        @php
                                            $result=$product->costBaseOnCurrentDayPredict("number_format","result") ;
                                            // همه ردیف های قیمت گذاری باید مقدار داشته باشند و بدون خطا
                                            $all_pricing_claculated=$all_pricing_claculated&$result["result"];
                                        @endphp
                                        {!! $result["result"]?$result["html_tree"]:$result["error"] !!}
                                    </td>
                                @else
                                    <td>{!! $product->costBaseOnAverageLatestPricePredict("number_format") !!}</td>
                                    <td>{!! $product->costBaseOnLatestPricePredict("number_format") !!}</td>

                                    <td>
                                        @php
                                            $result=$product->costBaseOnCurrentDayPredict("number_format","result") ;
                                        @endphp
                                        {!! $result["result"]?$result["html_tree"]:$result["error"] !!}

                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>


                </div>
                @break
        @endswitch
    @endforeach
    <div class="col-md-12">
        @if(!isset($show_btn_list) || $show_btn_list)
            @include($view_path."_btn_list")
        @endif
    </div>
</div>

