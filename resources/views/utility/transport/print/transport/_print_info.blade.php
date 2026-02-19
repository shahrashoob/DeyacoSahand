<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">

        <tr>
            <td colspan="7">
                {{$header_text}}
            </td>
        </tr>

        <tr>
            <td colspan="7">
                <table style="border: none">
                    <tr>
                        <td style="border: none; text-align: center">
                            <div style="font-size: .01px; position: fixed; left:0 ">
                                {{$qr}}
                            </div>
                        </td>
                        <td style="border: none; text-align: right;padding-right: 5px; font-size: 13px;">
                            شماره بار: {{$transport->getCode()}}
                            <br/>
                            تاریخ ایجاد بار: {{$transport->create_datetime()}}

                            <br/>
                            راننده: {{$transport->car->driver_firstname??""}}
                            {{$transport->car->driver_lastname??""}}
                            <br/>
                            شماره تماس راننده: {{$transport->car->driver_mobile??""}}

                            <br/>
                            پلاک خودرو: {{$transport->car->car_plaque??""}}

                            <br/>
                            نوع خودرو: {{$transport->car->car_type->caption??""}}


                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        @if($transport->transport_type_id ==1)

            <tr>
                <td style="width: 30px; font-size: 14px" colspan="7">
                    فرم های ورود موجود در بار
                    <br/>
                    @foreach($transport->transport_forms as $item)
                        {{$item->form->code}},
                    @endforeach
                </td>
            </tr>

        @else
            <tr>
                <td style="width: 30px; font-size: 14px" colspan="7">
                    برگ های خروج موجود در بار
                </td>
            </tr>
            <tr>
                <td>
                    شماره سفارش
                </td>
                <td>
                    نام مشتری
                </td>
                <td colspan="2">
                    شماره برگ خروج
                </td>
                <td colspan="5"></td>
            </tr>
            @foreach($list_order_info as $item)
                <tr>
                    <td>
                        @if($item->order)
                            {{$item->order->code()}}
                        @endif
                    </td>
                    <td>
                        @if($item->order && $item->order->customer )
                            {{$item->order->customer->fullCaption()}}
                        @endif
                    </td>
                    <td colspan="2">
                        {{"DCEF/".($item->form_id+1000)}}
                    </td>
                    <td colspan="5"></td>
                </tr>
            @endforeach
        @endif

        @if(count($product_list)>0)
            <tr>
                <th colspan="7">
                    لیست محتویات بار
                </th>
            </tr>
            <tr>

                <td>کد کالا</td>
                <td>نام کالا</td>
                <td>مقدار کل</td>
                <td>واحد کالا</td>
                <td>تعداد بسته بندی</td>
                <td>وزن خالص</td>
                <td>وزن ناخالص</td>
            </tr>
            @php $sum=0;$sum_packing_form=0;$sum_weight=0;$sum_gross_weight=0;@endphp
            @foreach($product_list as $product)
                @php $sum+=$product->sum_amount;@endphp

                <tr>
                    <td>  {{$product->code}}</td>
                    <td>  {{$product->caption}}</td>
                    <td>  {{round($product->sum_amount,4)}}  </td>
                    <td> {{$product->unit_caption}}</td>
                    @if(isset($product_weight[$product->id]))
                        <td>{{$product_weight[$product->id]["packing_count"]}}</td>
                        <td>{{$product_weight[$product->id]["weight"]}}</td>
                        <td>{{$product_weight[$product->id]["gross_weight"]}}</td>
                        @php $sum_packing_form+=$product_weight[$product->id]["packing_count"];@endphp
                        @php $sum_weight+=$product_weight[$product->id]["weight"];@endphp
                        @php $sum_gross_weight+=$product_weight[$product->id]["gross_weight"];@endphp
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
            <tr>
                <td colspan="2">جمع کل</td>
                <td>
                    {{round($sum,4)}}
                </td>
                <td></td>
                <td>
                    {{$sum_packing_form}}
                </td>
                <td>
                    {{$sum_weight}}
                </td>
                <td>
                    {{$sum_gross_weight}}
                </td>
            </tr>
            <tr>

        @endif
    </table>
</div>
<div style="text-align: center; width: 100%;padding:5px;font-size: 0.02px">
    {!! $barcode !!}
</div>
<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>
