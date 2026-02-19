<div class="card">
    <div class="card-header">
        <h5>لیست کارت های تولید


        </h5>
        <div style="margin-bottom: -15px">
            @include("production.details_dashboard._delete_filters")
            @include("production.details_dashboard._sort_cols")
            @include("production.details_dashboard._excel")
        </div>
    </div>

    <div class="card-block">

        <div class="table-responsive" style="min-height: 500px">
            <table class="table table-styling center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>

                        @include("production.details_dashboard._search_order")
                    </th>
                    <th>
                        تاریخ تایید پیش فاکتور
                        <br/>
                        تاریخ تحویل


                    </th>
                    <th>
                        @include("production.details_dashboard._search_order_status")

                    </th>
                    <th>
                        مقدار مجوز
                        <br/>
                        مقدار باقی مانده سفارش
                    </th>
                    <th>
                        @include("production.details_dashboard._search_production")
                    </th>
                    <th></th>
                    <th>
                        @include("production.details_dashboard._search_product")
                        @if(isset($production_channel_type_show_in_production_dashboard) && $production_channel_type_show_in_production_dashboard)

                        @endif
                    </th>
                    @if($property1_show_in_production_dashboard || $property2_show_in_production_dashboard || $production_channel_type_show_in_production_dashboard)
                        <th>

                            @include("production.details_dashboard._product_goods_kind_property")
                        </th>
                    @endif
                    <th style="min-width: 100px">مقدار
                        <br/>
                        موجودی
                    </th>
                    <th style="min-width: 100px">مقدار تخصیص</th>
                    <th style="min-width: 100px">
                        مقدار تولید
                    </th>

                    <th>
                        @include("production.details_dashboard._search_parent_production")

                    </th>

                    <th>
                        @include("production.details_dashboard._search_parent_product")

                    </th>
                    <th style="min-width: 100px">مقدار
                        <br/>
                        موجودی
                    </th>
                    <th style="min-width: 100px">مقدار تخصیص</th>
                    <th style="min-width: 100px">
                        مقدار تولید
                    </th>
                    @if(isset($allow_allocation_view))
                        <th> حداکثر تاریخ تحویل</th>
                    @endif

                </tr>

                </thead>
                <tbody>
                @php $row=$list->firstItem();@endphp
                @foreach($list as $item)

                    @php
                        // اگر کارت بالاسری داشته باشد، مقدار سفارش را از آن انتخاب می کنیم در غیر این صورت مقدار سفارش خودش را در نظر می گیریم.
                            $order_list_carton=
                            isset($item->parent_production->order_list)?
                            $item->parent_production->order_list->carton:
                            (isset($item->order_list)?$item->order_list->carton:$item->number);

                            $order_list_product_id =
                            isset($item->parent_production->order_list)?
                            $item->parent_production->order_list->product_id:
                            (isset($item->order_list)?$item->order_list->product_id:$item->product_id);

                            $order_list_order_id= isset($item->parent_production->order_list)?
                            $item->parent_production->order_list->order_id:
                            (isset($item->order_list)?$item->order_list->order_id:0);

                            $product_request_amount=
                            isset( $product_request_form_list[$item->order_id??0][$order_list_product_id])?
                             $product_request_form_list[$item->order_id??0][$order_list_product_id]["amount_request"]:0;

                            $product_request_amount_sent=isset( $product_request_form_list[$item->order_id??0][$order_list_product_id])?
                             $product_request_form_list[$item->order_id??0][$order_list_product_id]["amount_sent"]:0;

                            $remaining_order= $product_request_amount_sent;

                    @endphp

                    @if(!isset($allow_allocation_view) || (isset($allow_allocation_view) && $item->number > $allocation_amount))
                        <tr>
                            <td>{{$row++}}</td>
                            <td>
                                @if($item->order && $item->customer_id )
                                    <a data-toggle="modal" class="order-modal" data-target="#exampleModalLong"
                                       data-id="{{$item->order_list->order_id??""}}" href="#!">
                                        {{$item->order_list?$item->order_list->order->code():"***"}}
                                    </a>

                                    <br/>
                                    @if($allow_show_customer_caption_in_production_dashboard)
                                        <span class="text-info">


                                            @if(isset($order_logs[$item->order_id]))
                                                {!! str_replace("تصویر تاییدیه از طرف مشتری",  $item->customer->caption,$order_logs[$item->order_id]->message->text??"")  !!}
                                            @else
                                                {{ $item->customer->caption}}
                                            @endif
                                    </span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($item->order)
                                    @if(isset($order_logs[$item->order_id]))
                                        {{$order_logs[$item->order_id]->get_date()}}

                                    @endif
                                    <br/>

                                    <span
                                            @if(isset($datetime_color[$item->order_id]))
                                                @if($datetime_color[$item->order_id]<= .25)
                                                    style="color: #47dbdf"
                                            @elseif($datetime_color[$item->order_id]<= .5)
                                                style="color: #26f117"
                                            @elseif($datetime_color[$item->order_id]<= .75)
                                                style="color: #f1ca09"
                                            @elseif($datetime_color[$item->order_id]<= 1)
                                                style="color: #ef840a"
                                            @else
                                                style="color: #ea1e1e"
                                               @endif
                                            @endif

                                   >
                                        {{$item->order->delivery_datetime()}}

                                        @if(isset($datetime_color[$item->order_id]) && $datetime_color[$item->order_id]>1)
                                            <i class="fas fa-bolt"></i>
                                        @endif
                                   </span>

                                @endif
                            </td>
                            <td>


                                @include("line_product_station.product.unit_of_measure_type._simple",["product"=>$item->product,"amount"=>$order_list_carton])

                                <br/>
                                {{$item->order_list->order->status->caption??""}}

                            </td>
                            <td>
                                {{--                                مقدار باقی مانده سفارش--}}
                                @if($item->order)
                                    {{$product_request_amount}}

                                    {{$item->product->unit->caption}}

                                    <br/>
                                    @include("component.progress._progress_type2",["value"=>round($remaining_order / $order_list_carton*100)])


                                    {{$remaining_order}}
                                    {{$item->product->unit->caption}}
                                @endif
                            </td>
                            <td>
                                <a href="{{route("production.dashboard.view_card",[$item->production_card_id,$back_url_type??""])}}">{{$item->serial(1)}}
                                    @if($item->production_type_id ==2)
                                        <i class="fa fa-vial text-dark"></i>
                                    @endif
                                </a><br/>
                                <span class="text-info"> {{$item->priority->caption??""}}</span> -
                                {{$item->getStatus()}}


                            </td>

                            <td>

                                @if(isset($machine_re_allocation[$item->production_card_id]))
                                    &nbsp;
                                    &nbsp;
                                    <a style="color: #0b0b0b"
                                       href="{{route("production.dashboard.reallocation",[$item->production_card_id,$machine_re_allocation[$item->production_card_id]])}}">

                                        <i class="fas fa-reply-all "></i>


                                    </a>
                                @else
                                    <a href="{{route("production.dashboard.machine_allocation",[$item->production_card_id])}}">

                                        <i class="fa fas fa-reply  "></i>

                                    </a>
                                @endif
                            </td>
                            <td>
                                @include("production.details_dashboard._consumed_product")

                                @if(isset($production_channel_type_list[$item->product_id]["caption"]))
                                    <span class="text-info"> {{$production_channel_type_list[$item->product_id]["caption"]}}</span>
                                @endif
                                <br/>

                            </td>

                            @if($property1_show_in_production_dashboard || $property2_show_in_production_dashboard || $production_channel_type_show_in_production_dashboard)

                                <td>
                                    @if(isset($production_channel_type_list[$item->product_id]["color"]))
                                        @include("component.input._color_label",["color"=>$production_channel_type_list[$item->product_id]["color"]])
                                    @endif
                                    @include("production.dashboard._property")
                                </td>

                            @endif

                            <td style="vertical-align: top !important;">
                                {{$item->number}} {{$item->product->unit->bach_caption}}
                                @if($item->order_list_id)
                                    @include("component.progress._progress_type2",["value"=>round($item->number / $order_list_carton*100)])
                                @endif

                                {{$product_inventory[$item->product_id]}} {{$item->product->unit->bach_caption}}
                            </td>
                            <td style="vertical-align: top !important;">
                                @php $allocation_amount=isset($allocation_amount_list[$item->id])?$allocation_amount_list[$item->id]:0; @endphp
                                {{$allocation_amount}} {{$item->product->unit->bach_caption}}
                                @if($item->order_list_id)
                                    @include("component.progress._progress_type2",["value"=>round($allocation_amount / $order_list_carton*100)])
                                @endif

                                @if( $item->number > 0)
                                    @include("component.progress._progress_type2",["value"=>round($allocation_amount / $item->number*100),"class"=>"progress-c-theme"])
                                @else
                                    {{$item->number}}
                                @endif
                            </td>
                            <td style="vertical-align: top !important;">
                                @php $production_amount=isset($production_amount_list[$item->id])?$production_amount_list[$item->id]["amount"]:0; @endphp
                                {{$production_amount}} {{$item->product->unit->bach_caption}}
                                @if($item->order_list_id)
                                    @include("component.progress._progress_type2",["value"=>round($production_amount / $order_list_carton*100)])
                                @endif

                                @if( $item->number > 0)
                                    @include("component.progress._progress_type2",["value"=>round($production_amount / $item->number*100),"class"=>"progress-c-theme"])
                                @endif

                                {{isset($production_amount_list[$item->id])?$production_amount_list[$item->id]["caption"]:""}}

                            </td>

                            <td style="vertical-align: top !important;">
                                @if($item->parent_production)
                                    <a href="{{route("production.dashboard.view_card",[$item->parent_production,"production.dashboard.index"])}}">

                                        {{$item->parent_production->serial(1)}}
                                    </a>
                                    <br/>
                                    {{$item->parent_production->priority->caption}}
                                    -
                                    <span class="text-info">     {{$item->parent_production->getStatus()}}
   </span>

                                @endif
                            </td>
                            <td style="vertical-align: top !important;">
                                @if($item->parent_production)

                                    {{$item->parent_production->product->caption}}

                                    <br/>


                                    <span class="text-info">  {{isset($production_channel_type_parent_list[$item->parent_production->product_id])?$production_channel_type_parent_list[$item->parent_production->product_id]:""}}
                                       </span>
                                @endif
                            </td>
                            <td style="vertical-align: top !important;">
                                @if($item->parent_production)
                                    {{$item->parent_production->number}} {{$item->product->unit->bach_caption}}
                                    @if($item->order_list_id)
                                        @include("component.progress._progress_type2",["value"=>round($item->parent_production->number / $order_list_carton*100)])
                                    @endif

                                    {{$product_inventory[$item->parent_production->product_id]}} {{$item->product->unit->bach_caption}}
                                @endif
                            </td>
                            <td style="vertical-align: top !important;">
                                @if($item->parent_production)
                                    @php $allocation_amount=isset($allocation_amount_list[$item->parent_production_id])?$allocation_amount_list[$item->parent_production_id]:0; @endphp
                                    {{$allocation_amount}} {{$item->product->unit->bach_caption}}
                                    @if($item->order_list_id)
                                        @include("component.progress._progress_type2",["value"=>round($allocation_amount / $order_list_carton*100)])
                                    @endif

                                    @include("component.progress._progress_type2",["value"=>round($allocation_amount / $item->number*100),"class"=>"progress-c-theme"])
                                @endif
                            </td>
                            <td style="vertical-align: top !important;">
                                @if($item->parent_production)
                                    @php $production_amount=isset($production_amount_list[$item->parent_production_id])?$production_amount_list[$item->parent_production_id]["amount"]:0; @endphp
                                    {{$production_amount}} {{$item->product->unit->bach_caption}}
                                    @if($item->order_list_id)
                                        @include("component.progress._progress_type2",["value"=>round($production_amount / $order_list_carton)])
                                    @endif
                                    {{----}}
                                    @include("component.progress._progress_type2",["value"=>round($production_amount / $item->number*100),"class"=>"progress-c-theme"])
                                @endif
                            </td>


                            @if(isset($allow_allocation_view) && $item->order)
                                <td>{{$item->order->delivery_datetime()}} </td>
                            @endif


                        </tr>
                    @endif

                @endforeach
                </tbody>

            </table>
        </div>


        <div class="float-left">
            نمايش رکوردهای
            <b>{{$list->firstItem()}}</b>
            تا
            <b>{{$list->lastItem()}}</b>
            از
            <b>{{$list->total()}}</b>
            رکورد موجود


        </div>
    </div>
    <div class="text-center">
        {{$list->links('pagination::bootstrap-4')}}
    </div>
</div>