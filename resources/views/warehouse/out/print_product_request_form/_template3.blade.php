<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        <tr>
            <td colspan="{{8+($dashboard_type=="customer"?1:0)}}">
                {{$software_name}}
                <br/>
                @if(isset($dashboard_type) && $dashboard_type=="customer")
                    لیست درخواست های کالا از انبار -  {{$product_request_form->applicant->fullCaption()}}
                @else

                    فرم درخواست کالا از انبار -{{$product_request_form->code}}
                @endif
            </td>
        </tr>

        <tr>
            <td colspan="{{8+($dashboard_type=="customer"?1:0)}}">

                <table style="border: none">
                    <tr>
                        <td style="border: none">
                            <div style="font-size: .01px; position: fixed; left:0 ">
                                {{$qr}}
                            </div>
                        </td>
                        <td style="border:none; font-size: 16px; text-align: right; padding-right: 20px">


                            درخواست دهنده
                            :
                            {{$product_request_form->applicant->fullCaption()}}

                            @if(isset($dashboard_type) && $dashboard_type=="customer")

                            @else
                                <br/>
                                شماره مرجع ({{$product_request_form->applicant_type->reference_caption}}
                                )
                                :
                                {{$product_request_form->getReferenceNumber()}}
                            @endif
                            <br/>
                            تاریخ و زمان ارسال کالا
                            :
                            {{$product_request_form->coordinate_date_time()}}
                            <br/>
                            تاریخ دریافت گزارش
                            :
                            {{$date_now}}


                        </td>
                    </tr>
                </table>


            </td>
        </tr>


        <tr>
            <th class="btn-glow-primary">ردیف</th>
            <th>کد کالا</th>
            <th> نام کالا</th>
            @if(isset($dashboard_type) && $dashboard_type=="customer")
                <th>شماره درخواست</th>
            @endif
            <th>نوع بسته بندی</th>
            <th> واحد کالا</th>
            <th> درخواست</th>
            <th> تحویل شده</th>
            <th> باقی مانده</th>
        </tr>

        @php $row=0;@endphp
        @foreach($product_request_form_items as $item)
            <tr>
                <td>{{++$row}}</td>

                <td>{{$item->product->code}} </td>
                <td>{{$item->product->caption}}                </td>
                @if(isset($dashboard_type) && $dashboard_type=="customer")
                    <td>{{$item->product_request_form->code}}</td>
                @endif
                <td style="font-size: 10px">
                    {{--                    @if($item->product_request_form_packing_types()->distinct("packing_type_id")->count()!=1)--}}
                    @foreach($item->product_request_form_packing_types()->groupby("packing_type_id")->get() as $item_p)
                        {{$item_p->packing_type->code}} - {{$item_p->packing_type->caption}} <br/>
                    @endforeach

                    {{--                    @else--}}
                    {{--                        {{$item->product_request_form_packing_types()->groupby("packing_type_id")->first()->packing_type->caption}}--}}
                    {{--                    @endif--}}
                </td>

                <td> {{$item->product->unit->caption}}</td>
                <td>{{$item->amount_request}}  </td>

                <td>
                    {{round($item->amount_sent,4)}}

                </td>
                <td>{{$item->amount_remaining}} </td>


            </tr>
        @endforeach


    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 9px">
    سازمان دیجیتال دیاکو
</div>
