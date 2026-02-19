<div class="table-responsive">
    <table class="table table-styling center" style="">
        <thead>
        <tr>
            <th>ردیف</th>
            <th>تصویر کالا</th>
            <th>کد کالا</th>
            <th> عنوان کالا</th>
            <th></th>
            <th>مقدار انتخاب شده <br/>جهت خروج</th>
            <th></th>
            <th> درجه کالا</th>
            <th> واحد سنجش</th>
            <th> حداقل/حداکثر تعداد
                <br/>
                بسته بندی
            </th>
            <th> مقدار درخواست</th>
            @if($selected_amount["ShowWarehouseInventory"])
                <th> مقدار موجودی</th>
            @endif
            <th> مقدار تحویل شده</th>
            <th> مقدار باقی مانده</th>
            <th> مقدار در حال تحویل</th>
            <th>نوع بسته بندی</th>
        </tr>

        </thead>
        <tbody>
        @php $row=0; $CurrentSelectedToExist_amount =[]; $CurrentSelectedToExist_count=[];$currentDelivery_amount=0;$currentDelivery_count=0; $sum_amount_request=0;$sum_amount_sent=0;$sum_amount_remaining=0; @endphp
        @foreach($product_request_form_items as $item)
            @php
                $class_warning="";
                if(
                    isset($product_inventory[$item->product_id]) &&
    //                $item->product->min_inventory>0 &&
                    $item->product->min_inventory > $product_inventory[$item->product_id]-$currentSelectedToExist[$item->product_id]
                    ){
                   $class_warning="alert-warning";
                }
            @endphp
            <tr class="{{$class_warning}}">
                <td>{{++$row}}</td>
                <td>

                    <a href="" data-lightbox="1" data-title="My caption 1">
                        <img src="" style="width: 50px" alt="" class="img-fluid img-thumbnail">
                    </a>

                </td>
                <td>{{$item->product->code}}</td>
                <td>{{$item->product->caption}}                </td>
                <td>
                    @if(!isset($item->amount_remaining) || $item->amount_remaining !=0 )

                        @if(in_array( $product_request_form->status_id, [7005001,7005005,7005004,7005008]) )

                            @if(isset($allow_select_product) && $permission_confirm)
                                <a href="{{route("wh.out.delivery.index",[$product_request_form,$item->product_id,$page??1])}}">
                                    <i
                                            class="fa fa-plus"></i> </a>
                            @endif

                        @endif
                    @endif
                </td>
                <td>
                    {{--                    @if(!isset($item->amount_remaining) || $item->amount_remaining !=0 )--}}

                    @if(in_array( $product_request_form->status_id, [7005001,7005005,7005004,7005008]) )

                        {{$selected_amount[$item->id]["CurrentSelectedToExist"]["count"]}} بسته
                        ,
                        {{$selected_amount[$item->id]["CurrentSelectedToExist"]["amount"]+0}}
                        {{$item->product->unit->caption}}

                        @php
                            $CurrentSelectedToExist_amount[$item->product_id]=$selected_amount[$item->id]["CurrentSelectedToExist"]["amount"];
                            $CurrentSelectedToExist_count[$item->product_id]=$selected_amount[$item->id]["CurrentSelectedToExist"]["count"];
                            $sum_amount_request+=$item->amount_request;
                            $sum_amount_sent+=$item->amount_sent;
                            $sum_amount_remaining+=$item->amount_remaining;
                        @endphp

                    @endif
                    {{--                    @endif--}}
                </td>
                <td>
                    <a title="تغییر در درخواست"
                       href="{{route("utility.special_license.panel.new_special_license.index",[8,$product_request_form->id,$item->id])}}">
                        <i class="fa fa-unlock-alt"></i>
                        <i style="margin-right: -5px; " class="fa  fa-exchange-alt"></i>

                    </a>
                </td>
                <td>
                    @if($item->product_request_form_packing_types()->distinct("degree_id")->count()!=1)
                        <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-haspopup="true" style="width: 140px" aria-expanded="false">
                            {{$item->product_request_form_packing_types()->distinct("degree_id")->count()}}
                            نوع درجه
                        </button>
                        <div class="dropdown-menu" style="text-align: center">

                            @foreach($item->product_request_form_packing_types()->groupby("degree_id")->get() as $item_p)
                                <a href="#12" class="dropdown-item"
                                   id="btn_confirm_print4">  {{$item_p->degree->caption}}</a>
                            @endforeach


                        </div>
                    @else
                        {{$item->product_request_form_packing_types()->groupby("degree_id")->first()->degree->caption}}
                    @endif
                </td>
                <td>{{$item->product->unit->caption}}</td>
                <td>{{$item->min_number_of_packing_forms?? "-"}} / {{$item->max_number_of_packing_forms?? "-"}}</td>
                <td>{{is_null($item->amount_request)?"":$item->amount_request}} </td>
                @if(isset($selected_amount["ShowWarehouseInventory"]))
                    <td>
                        {{$selected_amount[$item->id]["WarehouseInventory"]["count"]}} بسته
                        ,
                        {{$selected_amount[$item->id]["WarehouseInventory"]["amount"]+0}}
                        {{$item->product->unit->caption}}
                    </td>
                @endif

                <td>
                    {{round($item->amount_sent,4)}}

                </td>
                <td>{{is_null($item->amount_remaining)?"":$item->amount_remaining}}</td>


                <td>
                    @if($selected_amount[$item->id]["CurrentDelivery"]["packing_form_count"]>0)
                        {{$selected_amount[$item->id]["CurrentDelivery"]["packing_form_count"]+0}}
                        بسته,
                        {{$selected_amount[$item->id]["CurrentDelivery"]["sum_amount"]+0}}
                        {{$item->product->unit->caption}}

                        @php
                            $currentDelivery_amount+=$selected_amount[$item->id]["CurrentDelivery"]["sum_amount"];
                            $currentDelivery_count+=$selected_amount[$item->id]["CurrentDelivery"]["packing_form_count"];

                        @endphp
                    @endif
                </td>
                <td>
                    @if($item->product_request_form_packing_types()->distinct("packing_type_id")->count()!=1)
                        <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-haspopup="true" style="width: 140px" aria-expanded="false">
                            {{$item->product_request_form_packing_types()->distinct("packing_type_id")->count()}}
                            بسته بندی مجاز
                        </button>
                        <div class="dropdown-menu" style="text-align: center">

                            @foreach($item->product_request_form_packing_types()->groupby("packing_type_id")->get() as $item_p)
                                <a href="#12" class="dropdown-item"
                                   id="btn_confirm_print4">{{$item_p->packing_type->code}}
                                    - {{$item_p->packing_type->caption}}</a>
                            @endforeach


                        </div>
                    @else
                        {{$item->product_request_form_packing_types()->groupby("packing_type_id")->first()->packing_type->caption}}
                    @endif
                    <a title="اضافه کردن بسته بندی جدید"
                       href="{{route("utility.special_license.panel.new_special_license.index",[4,$product_request_form->id,$item->id])}}"><i
                                class="fa fa-unlock-alt"></i></a>
                </td>
            </tr>
        @endforeach
        <td colspan="5"></td>
        <td>
            {{array_sum($CurrentSelectedToExist_count)}} بسته
            ,
            {{array_sum($CurrentSelectedToExist_amount)}}   {{$item->product->unit->caption}}
        </td>
        <td colspan="4"></td>
        @if(isset($selected_amount["ShowWarehouseInventory"]))
            <td></td>
        @endif
        <td>{{$sum_amount_request}}</td>
        <td>{{$sum_amount_sent}}</td>
        <td>{{$sum_amount_remaining}}</td>
        <td>
            @if($currentDelivery_count>0)
                {{$currentDelivery_count}} بسته
                ,
                {{$currentDelivery_amount}}   {{$item->product->unit->caption}}
            @endif
        </td>
        </td>
        <td colspan="9"></td>
        </tbody>
    </table>
</div>
