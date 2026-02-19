<div class="card">
    <div class="card-header">
        <h5>لیست کارت های تولید</h5>
    </div>
    <div class="card-block">

        <div class="table-responsive">
            <table class="table table-styling center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>سریال تولید</th>
                    <th></th>
                    <th>نام محصول
                    @if(isset($production_channel_type_show_in_production_dashboard) && $production_channel_type_show_in_production_dashboard)
                        <br/>
                        <span class="text-info">نام اولین کانال تولید در مسیر محصول</span>
                    @endif
                    </th>
                    @if($property1_show_in_production_dashboard || $property2_show_in_production_dashboard || $production_channel_type_show_in_production_dashboard)
                        <th>مشخصه کالا</th>
                    @endif
                    <th>مقدار</th>
                    <th>مقدار تخصیص</th>
                    <th>اولویت</th>
                    <th>وضعیت</th>
                    <th>سریال سطح بالا</th>
                    @if(isset($allow_allocation_view))
                        <th> حداکثر تاریخ تحویل</th>
                    @endif
                    <th>سفارش</th>
                </tr>

                </thead>
                <tbody>
                @php $row=$list->firstItem();@endphp
                @foreach($list as $item)
                    @php $allocation_amount=$item->get_allocation_amount(false,1); @endphp
                    @if(!isset($allow_allocation_view) || (isset($allow_allocation_view) && $item->number > $allocation_amount))
                        <tr>
                            <td>{{$row++}}</td>

                            <td>
                                <a href="{{route("production.dashboard.view_card",[$item->production_card_id,$back_url_type??""])}}">{{$item->serial(1)}}
                                    @if($item->production_type_id ==2)
                                        <i class="fa fa-vial text-dark"></i>
                                    @endif
                                </a>
                            </td>

                            <td>

                                @if(isset($machine_re_allocation[$item->production_card_id]))
                                    &nbsp;
                                    &nbsp;
                                    <a style="color: #0b0b0b" href="{{route("production.dashboard.reallocation",[$item->production_card_id,$machine_re_allocation[$item->production_card_id]])}}">

                                        <i class="fas fa-reply-all "></i>


                                    </a>
                                @else
                                    <a href="{{route("production.dashboard.machine_allocation",[$item->production_card_id])}}">

                                        <i class="fa fas fa-reply  "></i>

                                    </a>
                                @endif
                            </td>
                            <td>{{$item->product->code." - ".$item->product->caption}}
                            @if(isset($production_channel_type_list[$item->product_id]["caption"]))
                                <br/>
                               <span class="text-info"> {{$production_channel_type_list[$item->product_id]["caption"]}}</span>
                                @endif
                            </td>

                            @if($property1_show_in_production_dashboard || $property2_show_in_production_dashboard || $production_channel_type_show_in_production_dashboard)

                                <td>
                                    @if(isset($production_channel_type_list[$item->product_id]["color"]))
                                        @include("component.input._color_label",["color"=>$production_channel_type_list[$item->product_id]["color"]])
                                    @endif
                                    @include("production.dashboard._property")
                                </td>

                            @endif

                            <td>{{$item->number}} {{$item->product->unit->bach_caption}}</td>
                            <td>{{$allocation_amount}} {{$item->product->unit->bach_caption}}</td>

                            <td>{{$item->priority->caption??""}}</td>
                            <td>{{$item->getStatus()}}</td>
                            <td>
                                @if($item->parent_production)
                                    <a href="{{route("production.dashboard.view_card",[$item->parent_production,"production.dashboard.index"])}}">

                                    {{$item->parent_production->serial(1)}}
                                    </a>
                                    <br/>
                                    <span class="text-info">  {{isset($production_channel_type_parent_list[$item->parent_production->product_id])?$production_channel_type_parent_list[$item->parent_production->product_id]:""}}
                                    </span>
                                @endif
                            </td>
                            @if(isset($allow_allocation_view) && $item->order)
                                <td>{{$item->order->delivery_datetime()}} </td>
                            @endif
                            <td>
                                @if($item->order && $item->customer_id )
                                    {{$item->order->code()}}
                                @endif
                            </td>

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