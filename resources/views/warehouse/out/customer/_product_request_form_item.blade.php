<div class="table-responsive">
    <table class="table table-styling center" style="">
        <thead>
        <tr>
            <th>ردیف</th>
            <th>کد کالا</th>
            <th> عنوان کالا</th>

            <th></th>
            <th></th>
            <th>مقدار انتخاب شده <br/>جهت خروج</th>
            <th></th>
            <th> درجه کالا</th>

            <th> حداقل/حداکثر تعداد
                <br/>
                بسته بندی
            </th>
            <th> مقدار درخواست</th>
            <th></th>
            <th> مقدار تحویل شده</th>
            <th> مقدار باقی مانده</th>
            <th> مقدار در حال تحویل</th>
            <th>نوع بسته بندی</th>
        </tr>

        </thead>
        <tbody>
        @php $row=$product_request_form_item_by_product->firstItem();
                $sum_selected_amount=0;
                $sum_selected_packing=0;
        @endphp
        @foreach($product_request_form_item_by_product as $item)

            <tr>
                <td>{{$row++}}</td>


                <td>{{$item->product->code}}</td>
                <td>
                    @if(count($product_request_form_items_by_product_details[$item->product_id]) > 1)
                        <a class="" data-toggle="collapse" href="#product_row{{$item->product_id}}" role="button"
                           aria-expanded="true"
                           aria-controls="multiCollapseExample1">

                            {{$item->product->caption}}  </a>
                    @else
                        {{$item->product->caption}}
                    @endif

                </td>
                <td>
                    @if(count($product_request_form_items_by_product_details[$item->product_id]) > 1)
                        <a class="" data-toggle="collapse" href="#product_row{{$item->product_id}}" role="button"
                           aria-expanded="true"
                           aria-controls="multiCollapseExample1">
                            {{count($product_request_form_items_by_product_details[$item->product_id])}}
                            درخواست
                        </a>
                    @else
                        {{$product_request_form_items_by_product_details[$item->product_id][0]->product_request_form_code}}
                    @endif
                </td>
                <td>
                    <a href="{{route("wh.out.delivery.index",[$item->product_request_form_id,$item->product,$product_request_form_item_by_product->currentPage(),"customer"])}}">
                        <i class="fa fa-plus"></i> </a>
                </td>
                <td>
                    @if(isset($current_selected_to_exit[$item->product_id]))
                        {{count($current_selected_to_exit[$item->product_id]["number_packing_forms"])}}
                        بسته بندی
                        {{$current_selected_to_exit[$item->product_id]["amounts"]}}
                        {{$item->product->unit->caption??"---"}}

                        @php
                            $sum_selected_packing+=count($current_selected_to_exit[$item->product_id]["number_packing_forms"]);
                            $sum_selected_amount+=$current_selected_to_exit[$item->product_id]["amounts"];
                        @endphp
                    @else
                        0 بسته بندی  0
                {{$item->product->unit->caption??"---"}}
                @endif
                <td>
                    @if(count($product_request_form_items_by_product_details[$item->product_id]) == 1)
                        <a title="تغییر در درخواست"
                           href="{{route("utility.special_license.panel.new_special_license.index",[8,$item->product_request_form_id,$item->id])}}">
                            <i class="fa fa-unlock-alt"></i>
                            <i style="margin-right: -5px; " class="fa  fa-exchange-alt"></i>

                        </a>
                    @endif
                </td>
                <td style="width: 60px">
                    @if(count($product_request_form_items_by_product_details[$item->product_id]) == 1)
                        @foreach($packing_types["degree"][$item->id] as $d_item)
                            {{$d_item}}
                        @endforeach
                    @endif
                </td>

                <td>
                    @if(count($product_request_form_items_by_product_details[$item->product_id]) == 1)
                        {{$item_details->min_number_of_packing_forms?? "-"}}
                        / {{$item_details->max_number_of_packing_forms?? "-"}}
                    @endif
                </td>
                <td>{{$item->sum_amount_request}}</td>

                <td>

                </td>

                <td>
                    {{round($item->sum_amount_sent,4)}}

                </td>
                <td>{{$item->sum_amount_remaining}}</td>
                <td>
                    @if(isset($current_delivery["products"][$item->product_id]))
                        {{count($current_delivery["products"][$item->product_id]["number_packing_forms"])}}
                        بسته بندی ,
                        {{$current_delivery["products"][$item->product_id]["amounts"]}}
                        {{$item->product->unit->caption??"---"}}
                    @else
                        0 بسته بندی ,  0
                        {{$item->product->unit->caption??"---"}}
                    @endif
                </td>

                <td>
                    @if(count($product_request_form_items_by_product_details[$item->product_id]) == 1)
                        @foreach($packing_types["packing_types"][$item->id] as $d_item)
                            {{$d_item}}
                        @endforeach
                    @endif
                </td>
            </tr>
            @if(count($product_request_form_items_by_product_details[$item->product_id]) > 1)
                @foreach($product_request_form_items_by_product_details[$item->product_id] as $item_details)

                    <tr class="alert alert-info multi-collapse mt-2 collapse " id="product_row{{$item->product_id}}">

                        <td>


                        </td>

                        <td colspan="2">
                        <td>


                            {{$item_details->product_request_form_code}}

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>
                            <a title="تغییر در درخواست"
                               href="{{route("utility.special_license.panel.new_special_license.index",[8,$item_details->product_request_form_id,$item_details->id])}}">
                                <i class="fa fa-unlock-alt"></i>
                                <i style="margin-right: -5px; " class="fa  fa-exchange-alt"></i>

                            </a>
                        </td>
                        <td>

                            @foreach($packing_types["degree"][$item->id] as $d_item)
                                {{$d_item}}
                            @endforeach
                        </td>

                        <td>
                            {{$item_details->min_number_of_packing_forms?? "-"}}
                            / {{$item_details->max_number_of_packing_forms?? "-"}}
                        </td>
                        <td>{{$item_details->amount_request}}</td>

                        <td>

                        </td>

                        <td>
                            {{round($item_details->amount_sent,4)}}

                        </td>
                        <td>{{$item_details->amount_remaining}}</td>
                        <td>


                        </td>

                        <td>
                            <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button"
                                    data-toggle="dropdown"
                                    aria-haspopup="true" style="width: 140px" aria-expanded="false">
                                {{count($packing_types["packing_types"][$item->id])}}
                                بسته بندی مجاز
                            </button>
                            <div class="dropdown-menu" style="text-align: center">

                                @foreach($packing_types["packing_types"][$item->id] as $item_p)
                                    <a href="#12" class="dropdown-item"
                                       id="btn_confirm_print4">{{$item_p}}
                                        - {{$item_p}}</a>
                                @endforeach


                            </div>
                            <a title="اضافه کردن بسته بندی جدید"
                               href="{{route("utility.special_license.panel.new_special_license.index",[4,$item_details->product_request_form_id,$item_details->id])}}"><i
                                        class="fa fa-unlock-alt"></i></a>
                        </td>
                    </tr>
                @endforeach
            @endif
        @endforeach
        @if($product_request_form_item_by_product->lastPage() >1 )
            <tr>
                <td colspan="4">جمع در صفحه
                <td>
                <td>
                    {{$sum_selected_packing}}
                    بسته بندی
                    ,
                    {{$sum_selected_amount}}
                    {{$item->product->unit->caption??"---"}}
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="4">جمع کل
            <td>
            <td>
                {{$all_selected_to_exit["number_packing_forms"]}}
                بسته بندی
                ,
                {{$all_selected_to_exit["amount"]}}
                {{$item->product->unit->caption??"---"}}
            </td>
        </tr>
        </tbody>
    </table>
    <div class="float-left">
        نمايش رکوردهای
        <b>{{$product_request_form_item_by_product->firstItem()}}</b>
        تا
        <b>{{$product_request_form_item_by_product->lastItem()}}</b>
        از
        <b>{{$product_request_form_item_by_product->total()}}</b>
        رکورد موجود

    </div>
    <div class="text-center">
        {{$product_request_form_item_by_product->links('pagination::bootstrap-4')}}
    </div>
</div>