<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h5> برگ برنامه ریزی سفارش {{$order_list->id}}</h5>
        </div>
        <div class="card-block" style="overflow: auto">
            <table class="table">
                <tr>
                    <td>
                        سفارش
                    </td>
                    <td>
                        @if(isset($order_list->order_id) && $order_list->order_id!=0)
                            <a href="{{route("utility.planing.view_order",$order_list->order_id)}}"> {{$order_list->order->code()}}
                                </a>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td> مشتری</td>
                    <td> {{$order_list->customer->caption??""}}</td>
                </tr>
                <tr>
                    <td> کالا</td>
                    <td> {{$order_list->product->code??""}} - {{$order_list->product->caption??""}}</td>
                </tr>
                <tr>
                    <td>شماره فراخوانی</td>
                    <td>{{$order_list->call_id??""}}</td>
                </tr>
                <tr>
                <tr>
                    <td>تعداد کارتن</td>
                    <td>{{$order_list->carton}}</td>
                </tr>
                <tr>
                    <td>تعداد  در کارتن</td>
                    <td>{{$order_list->number_in_carton}}</td>
                </tr>
                <tr>
                    <td>مقدار</td>
                    <td>{{$order_list->amount}}</td>
                </tr>
                <tr>
                    <td>وضعیت سفارش</td>
                    <td>{{$order_list->order_status->caption??""}}</td>
                </tr>
                <tr>
                    <td>وضعیت erp</td>
                    <td>{{$order_list->status->caption??""}}</td>
                </tr>
                <tr>
                    <td>وضعیت پردازش</td>
                    <td>{{$order_list->processing_status->caption??""}}</td>
                </tr>
                <tr>
                    <td>وضعیت بارگیری</td>
                    <td>{{$order_list->loadable_status->caption??""}}</td>
                </tr>
                <tr>
                    <td>شرح برگه</td>
                    <td>{{$order_list->description_sheet->text??""}}</td>
                </tr>
                <tr>
                    <td>شرح درخواست</td>
                    <td>{{$order_list->description_request->text??""}}</td>
                </tr>
                <tr>
                    <td>مقدار ارسال شده </td>
                    <td>{{$order_list->amount_sent??""}}</td>
                </tr>
                <tr>
                    <td>مقدار  باقی مانده </td>
                    <td>{{$order_list->amount_remaining??""}}</td>
                </tr>
                <tr>
                    <td>اضافه تولید   </td>
                    <td>{{$order_list->amount_ep??""}}</td>
                </tr>
                <tr>
                    <td> موجودی   </td>
                    <td>{{$order_list->inventory??""}}</td>
                </tr>
                <tr>
                    <td> جمع کارت های در انتظار   </td>
                    <td>{{$order_list->sum_wpc??""}}</td>
                </tr>
                <tr>
                    <td> جمع سفارش های در انتظار   </td>
                    <td>{{$order_list->sum_wo??""}}</td>
                </tr>
                <tr>
                    <td> بچ تولید   </td>
                    <td>{{$order_list->bp??""}}</td>
                </tr>
                <tr>
                    <td> حداقل موجودی  </td>
                    <td>{{$order_list->mi??""}}</td>
                </tr>
                <tr>
                    <td> حداقل تولید  </td>
                    <td>{{$order_list->mp??""}}</td>
                </tr>
                <tr>
                    <td> مقدار مورد نیاز  </td>
                    <td>{{$order_list->po??""}}</td>
                </tr>
                <tr>
                    <td> تعداد کارت تولید  </td>
                    <td>{{$order_list->pc??""}}</td>
                </tr>
                <tr>
                    <td> بخش فعال کارت تولید  </td>
                    <td>{{$order_list->ap??""}}</td>
                </tr>
                <tr>
                    <td>تاریخ سفارش گذاری</td>
                    <td>{{$order_list->order_date()}}</td>
                </tr>
                <tr>
                    <td>تاریخ ایجاد</td>
                    <td>{{$order_list->get_create_date_and_time()}}</td>
                </tr>
                <tr>
                    <td>سفارش گذاری از </td>
                    <td>{{$order_list->form_order_list_id}}</td>
                </tr>
                <tr>
                    <td>نوع سفارش</td>
                    <td>
                        {{$order_list->order_kind->caption??""}}
                    </td>
                </tr>

            </table>

        </div>
    </div>
</div>
