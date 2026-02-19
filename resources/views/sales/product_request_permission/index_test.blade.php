@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  فروش ")

@section('content')
    @php $order_checkbox=[]; $active_inventory_check=[]; @endphp
    <form id="form1" autocomplete="off"
          action="{{route("sales.product_request_permission.submit",[$order])}}?page={{$product_request_form_items->currentPage()}}"
          method="post"
          novalidate="novalidate">
        @csrf
        @include("component.input._hidden",["id"=>"save_status","value"=>0])
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> سفارشات {{$order->customer->caption}} در یک نگاه </h5>
                    </div>

                    <div class="card-block">

                        <div class="row">


                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-styling center" style="">
                                        <thead>
                                        <tr>
                                            <th colspan="5"></th>
                                            <th style="background: #d5a6a6"
                                                colspan="3">    {{$order->customer->caption}}</th>
                                            <th style="background: #d8cccc" colspan="2"> سایر مشتریان</th>
                                            <td></td>
                                            <th style="background: #c2d6b6" colspan="3">موجودی</th>
                                            @if($does_sales_view_inventory_on_the_way)
                                                <th style="background: #d5e4cd" colspan="2">موجودی در راه</th>
                                            @endif
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>ردیف</th>
                                            <th>شماره سفارش</th>
                                            <th>نام و کد کالا</th>
                                            <th> واحد سنجش</th>

                                            <th> سفارش</th>

                                            <th>سایر سفارش ها</th>
                                            <th> مجوز صادر شده</th>

                                            <th> سفارش</th>
                                            <th>مجوز باقی مانده</th>

                                            <th> مجوز خروج جدید</th>
                                            <th class="green_row">موجودی آزاد</th>

                                            <th class="green_row"> بسته بندی های مجاز</th>
                                            <th class="green_row"> سایر بسته بندی ها</th>
                                            @if($does_sales_view_inventory_on_the_way)
                                                <th style="background: #d5e4cd"> بسته بندی های مجاز</th>
                                                <th style="background: #d5e4cd"> سایر بسته بندی ها</th>
                                            @endif

                                            <th>در حال تحویل</th>
                                            <th> تحویل شده</th>
                                            <th> باقی مانده مجوزها</th>
                                            <th> باقی مانده سفارش</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php
                                            $row=$product_request_form_items->firstItem();
                                            $allow_permission=false;
                                            $sum_order_amount=0;
                                            $sum_amount_request=0;
                                            $other_customer_order_amount=0;
                                            $other_customer_amount_request=0;
                                            $packing_form_inventory_other=0;
                                            $packing_form_inventory=0;
                                            $packing_form_inventory_in_the_way=0;
                                            $packing_form_inventory_in_the_way_other=0;

                                            $sum_amount_sent=0;
                                            $sum_amount_remainig=0;
                                            $sum_current_delivery=0;

                                        @endphp
                                        @foreach($product_request_form_items as $item)
                                            @php
                                                $value_order_list=isset($new_permission_list[$item->order_id][$item->order_list_id])?$new_permission_list[$item->order_id][$item->order_list_id]:$permission_amount[$item->order_list_id];
                                            $list_current_delivery_amount_order_list[$item->order_list_id]=isset($list_current_delivery_amount_order_list[$item->order_list_id])?$list_current_delivery_amount_order_list[$item->order_list_id]:0;
                                            $active_inventory_item=isset($active_inventory_list[$item->product_id])?max($active_inventory_list[$item->product_id],0):0;
                                            @endphp
                                            <tr

                                                    @if($item->order_amount <= $item->amount_sent)
                                                        class="alert-primary"

                                                    @elseif( $item->order_amount > $item->amount_sent && $item->order_amount <= ( $item->amount_sent+$list_current_delivery_amount_order_list[$item->order_list_id]))
                                                        class="alert-info"
                                                    @elseif($permission_amount[$item->order_list_id]==0 && (( $item->amount_request??0) < $item->order_amount ) )
                                                        class="alert-warning"


                                                    @elseif( $item->amount_request == $item->order_amount)
                                                        class="alert-success"
                                                    {{--                                                    @elseif( $item->order_amount < $item->amount_sent)--}}
                                                    {{--                                                        class="alert-success"--}}

                                                    @endif>
                                                <td style="text-align: left">


                                                    @if(!isset($order_checkbox[$item->order_id]) && $permission_amount[$item->order_list_id]>0 && $order_ids_count[$item->order_id]>1)
                                                        <input type="checkbox" class="checkbox_permission_order"
                                                               data-id="{{$item->order_id}}">
                                                        @php $order_checkbox[$item->order_id]=true; @endphp
                                                    @else
                                                        {{--                                                        <span class="fa fa-check-circle text-c-green"></span>--}}
                                                    @endif
                                                    @if($permission_amount[$item->order_list_id]>0)
                                                        <input type="checkbox"
                                                               class="
                                                                       checkbox_permission order_{{$item->order_id}}
                                                                       @if($permission_amount[$item->order_list_id]> $value_order_list)
                                                                            red_row
                                                                        @endif
                                                                       "

                                                               @if(isset($new_permission_list[$item->order_id][$item->order_list_id])) checked='checked'
                                                               @endif

                                                               name="checkbox_permission[{{$item->order_list_id}}]"
                                                               data-id="{{$item->order_list_id}}">
                                                        @php $allow_permission=true; @endphp
                                                    @else
                                                        {{--                                                        <span class="fa fa-check-circle text-c-green"></span>--}}
                                                    @endif
                                                    @if($item->amount_remaining> 0)
                                                        <a class="text-danger"
                                                           href="{{route('sales.product_request_permission.remove_permission_order_list',[$item->order_list_id,$item->product])}}">
                                                            <i class="fas fa-level-down-alt"></i>
                                                            <i style="margin-right: -5px"
                                                               class="fas fa-shopping-basket"></i>

                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{$row++}}</td>
                                                <td>

                                                    <a href="{{route("sales.dashboard.view_order",$item->order_id)}}">{{$item->order_series."/".$item->order_code}}</a>
                                                </td>
                                                <td>{{$item->product->caption}} <br/>
                                                    {{$item->product->code}}
                                                </td>
                                                <td>{{$item->product->unit->caption}}</td>


                                                {{--                                                مقدار درخواست مشتری--}}
                                                <td style="background: #d5a6a6">{{is_null($item->order_amount)?"":$item->order_amount}}</td>


                                                <td style="background: #d5a6a6">
                                                    <a href="{{route("sales.product_request_permission.get_other_customer_order",[$item->order_id,$item->product_id])}}">

                                                        {{max(0,(isset($other_customer_order_list[$item->product_id])?$other_customer_order_list[$item->product_id]:0)-(is_null($item->order_amount)?"":$item->order_amount)-0)}}
                                                    </a>
                                                </td>
                                                <td style="background: #d5a6a6">

                                                    <button class="text-primary" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false"
                                                            style="background: none;border: none;">

                                                        {{is_null($item->amount_request)?"":$item->amount_request}}

                                                    </button>
                                                    @if(isset($order_list_product_request_forms[$item->order_list_id]))
                                                        <div class="dropdown-menu center" x-placement="bottom-start"
                                                             style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(163px, 210px, 0px);">
                                                            @foreach($order_list_product_request_forms[$item->order_list_id] as $item_product_request_form)
                                                                <span class="dropdown-item"
                                                                      href="#">{{$item_product_request_form["code"]}}</span>

                                                            @endforeach


                                                        </div>

                                                    @endif

                                                </td>


                                                {{--                                                سایر مشتریان--}}
                                                <td>
                                                    <a href="{{route("sales.product_request_permission.get_other_order",[$item->order_id,$item->product_id])}}">
                                                        {{max(0,round($all_order_list[$item->product_id] -  (isset($other_customer_order_list[$item->product_id])?$other_customer_order_list[$item->product_id]:0),2))}}
                                                    </a>

                                                </td>
                                                <td>
                                                    <a href="{{route("sales.product_request_permission.get_other_customer_permission",[$item->order_id,$item->product_id])}}">
                                                        {{round($all_product_request_remaining_list[$item->product_id] +(isset( $list_current_delivery_amount[$item->product_id])?  $list_current_delivery_amount[$item->product_id]:0),2) }}
                                                    </a>
                                                </td>


                                                <td>
                                                    @if( $permission_amount[$item->order_list_id]>0)
                                                        <input type="number"
                                                               class="order_input_{{$item->order_id}} number_permission_amount product_{{$item->product_id}}"
                                                               max="{{$permission_amount[$item->order_list_id]}}"
                                                               data-id="{{$item->order_list_id}}"
                                                               name="product_permission[{{$item->order_list_id}}]"
                                                               id="product_permission_{{$item->order_list_id}}"
                                                               style="
                                                               width: 60px;
                                                               @if(!isset($new_permission_list[$item->order_id][$item->order_list_id])) display: none;@endif
                                                               @if($permission_amount[$item->order_list_id] > 0 &&$permission_amount[$item->order_list_id] <$item->order_amount - ($item->amount_request??0) ) background:#f17a7a @endif

                                                               "
                                                               required min="1"
                                                               value="{{$value_order_list}}"
                                                               data-avtive_inventory="{{$active_inventory_item}}"

                                                        >
                                                    @else
                                                        <samp title="موجودی فعال : {{$permission_amount[$item->order_list_id]}} ">
                                                            ---
                                                        </samp>
                                                    @endif

                                                </td>
                                                <td>
                                                    {{--                                                    موجودی آزاد--}}
                                                    {{$active_inventory_item}}
                                                </td>
                                                <td>
                                                    <button class="text-primary" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false"
                                                            style="background: none;border: none;">

                                                        {{round($packing_form_inventory_by_order_list[$item->order_list_id])}}

                                                    </button>
                                                    <div class="dropdown-menu center" x-placement="bottom-start"
                                                         style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(163px, 210px, 0px);">
                                                        @if(isset($order_list_packing_type_cation_list[$item->order_list_id]))
                                                            @foreach($order_list_packing_type_cation_list[$item->order_list_id] as $item_packing_type)
                                                                <span class="dropdown-item"
                                                                      href="#">{{$item_packing_type}}</span>
                                                                <a class="dropdown-item"
                                                                   href="{{route("sales.product_request_permission.show_packing_form_inventory",[$item->order_list_id,$item->product_id,"packing_inventory"])}}">
                                                                    مشاهده بسته بندی ها
                                                                </a>
                                                            @endforeach
                                                        @endif
                                                    </div>

                                                </td>
                                                @php
                                                    $sum_order_amount+=is_null($item->order_amount)?0:$item->order_amount ;
                                                    $sum_amount_request+=is_null($item->amount_request)?0:$item->amount_request;
                                                    $other_customer_order_amount+=$all_order_list[$item->product_id] - ($item->order_amount??0);
                                                    $other_customer_amount_request+=$all_product_request_remaining_list[$item->product_id] +(isset( $list_current_delivery_amount[$item->product_id])?  $list_current_delivery_amount[$item->product_id]:0);

                                                    $packing_form_inventory+=$packing_form_inventory_by_order_list[$item->order_list_id];
                                                    $packing_form_inventory_other+=$inventory_list[$item->product_id]-$packing_form_inventory_by_order_list[$item->order_list_id];

                                                    $packing_form_inventory_in_the_way+=$packing_form_on_the_way_by_order_list[$item->order_list_id]["allowed"];
                                                    $packing_form_inventory_in_the_way_other+=$packing_form_on_the_way_by_order_list[$item->order_list_id]["now_allowed"];

                                                    $sum_amount_sent+=round($item->amount_sent,4);
                                                    $sum_amount_remainig+=is_null($item->amount_remaining)?0:$item->amount_remaining;
                                                    $sum_current_delivery+=$list_current_delivery_amount_order_list[$item->order_list_id];
                                                @endphp
                                                <td>
                                                    {{--                                                    سایر بسته بندی ها--}}
                                                    <a href="{{route("sales.product_request_permission.show_packing_form_inventory",[$item->order_list_id,$item->product_id,"other_packing_inventory"])}}">
                                                                {{round($inventory_list[$item->product_id]-$packing_form_inventory_by_order_list[$item->order_list_id],2)}}
                                                    </a>
                                                </td>

                                                {{--                                            <td></td>--}}
                                                {{--                                            <td></td>--}}
                                                {{--                                            <td></td>--}}

                                                @if($does_sales_view_inventory_on_the_way)
                                                    <td>
                                                        {{round($packing_form_on_the_way_by_order_list[$item->order_list_id]["allowed"],2)}}
                                                    </td>
                                                    <td>
                                                        {{round($packing_form_on_the_way_by_order_list[$item->order_list_id]["now_allowed"],2)}}
                                                    </td>
                                                @endif
                                                <td>

                                                    {{$list_current_delivery_amount_order_list[$item->order_list_id]}}

                                                </td>
                                                <td>
                                                    {{round($item->amount_sent,4)}}

                                                </td>
                                                <td>{{is_null($item->amount_remaining)?"":$item->amount_remaining}}</td>
                                                <td>
                                                    {{max(0,(is_null($item->order_amount)?0:$item->order_amount) - round($item->amount_sent,4) - $list_current_delivery_amount_order_list[$item->order_list_id])}}
                                                </td>

                                            </tr>

                                        @endforeach
                                        <tr>
                                            <td colspan="5">
                                                جمع کل ( از رکورد
                                                {{$product_request_form_items->firstItem()}}
                                                تا
                                                {{$product_request_form_items->lastItem()}}
                                                )
                                            </td>
                                            <td>
                                                {{$sum_order_amount}}
                                            </td>
                                            <td>
                                                {{$sum_amount_request}}
                                            </td>
                                            <td></td>
                                            <td>
                                                {{$other_customer_order_amount}}
                                            </td>
                                            <td>
                                                {{$other_customer_amount_request}}
                                            </td>
                                            <td>
                                                <span id="number_permission_amount_sum"></span>
                                            </td>

                                            <td>


                                            </td>
                                            <td>
                                                {{$packing_form_inventory}}
                                            </td>
                                            <td>
                                                {{$packing_form_inventory_other}}
                                            </td>

                                            @if($does_sales_view_inventory_on_the_way)
                                                <td>
                                                    {{$packing_form_inventory_in_the_way}}
                                                </td>
                                                <td>
                                                    {{$packing_form_inventory_in_the_way_other}}
                                                </td>
                                            @endif

                                            <td>
                                                {{$sum_current_delivery}}
                                            </td>

                                            <td>
                                                {{$sum_amount_sent}}
                                            </td>
                                            <td>
                                                {{$sum_amount_remainig}}
                                            </td>
                                            <td>
                                                {{$sum_order_amount - $sum_amount_sent - $sum_current_delivery}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>


                                </div>

                                <div class="float-left">
                                    نمايش رکوردهای
                                    <b>{{$product_request_form_items->firstItem()}}</b>
                                    تا
                                    <b>{{$product_request_form_items->lastItem()}}</b>
                                    از
                                    <b>{{$product_request_form_items->total()}}</b>
                                    رکورد موجود


                                </div>
                                <div class="text-center">
                                    {{$product_request_form_items->links('pagination::bootstrap-4')}}
                                </div>
                            </div>
                            <div class="col-md-12 center">
                                <br/>
                                @if(isset($back_url))
                                    <a href="{{$back_url}}" class="btn btn-outline-dark">
                                        <i class="fa fa-arrow-right"></i> بازگشت
                                    </a>
                                @else
                                    <a href="{{route("sales.dashboard.index")}}" class="btn btn-outline-dark"
                                       type="button">
                                        بازگشت
                                    </a>
                                @endif


                                @if($does_sales_need_to_register_a_loading_permit_form)
                                    @if($allow_permission)
                                        <button
                                                class="btn btn-primary" type="submit">
                                            <i class="fa fa-unlock-alt"></i>
                                            ثبت مجوز بارگیری
                                            (دستور ارسال بار)
                                        </button>


                                        <button id="btn_save"
                                                class="btn btn-warning" type="submit">
                                            <i class="fa fa-save"></i>
                                            ذخیره اطلاعات
                                        </button>
                                    @else
                                        <a href="#"
                                           onclick="return alert('با توجه به اینکه برای تمامی سطرها مجوز خروج صادر شده است، امکان ثبت مجوز جدید وجود ندارد.')"
                                           class="btn btn-warning" type="submit">
                                            <i class="fa fa-unlock-alt"></i>
                                            ثبت مجوز بارگیری
                                            (دستور ارسال بار)
                                        </a>
                                    @endif
                                @endif

                                <a href="{{route("sales.product_request_permission.index_product",$order)}}"
                                   class="btn btn-outline-dark" type="button">
                                    مشاهده لیست اقلام به تفکیک کالا
                                </a>
                            </div>

                        </div>


                    </div>
                </div>

            </div>
            <div class="col-md-6">
                راهنمای رنگ ها:
                <br/>
                <div class="alert-success">
                    به اندازه مقدار سفارش، مجوز ارسال بار صادر شده است.
                </div>
                <div class="alert-primary">
                    به اندازه مقدار سفارش، کالا برای مشتری ارسال شده است.
                </div>
                <div class="alert-info">
                    به اندازه مقدار سفارش، بخشی از کالا برای مشتری ارسال شده است و بخشی در حال تحویل می باشد.
                </div>
                <div class="alert-warning">
                    به اندازه مقدار سفارش، مجوز ارسال بار ارسال نشده است و موجودی فعال کالا صفر می باشد.

                </div>
                <div class="alert-danger">
                    هشدار: ثبت مجوز خروج جدید به اندازه سفارش نمی باشد.

                </div>
            </div>
        </div>
    </form>
@endsection

@section("styles")
    <style>
        .red_row {
            background-color: #fccac7;
        }

        .green_row {
            background-color: #c2d6b6;
        }

        .table-responsive {
            height: 100vh; /* ارتفاع قابل اسکرول */
            overflow-y: auto;
            border: 1px solid #ccc;
        }

        thead th {
            position: sticky;
            top: -1px;
            background: #f7f7f7; /* رنگ پس‌زمینه برای خوانایی */
            z-index: 10;
        }

    </style>
@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "image_file": "required",
            }
        });
        $("#btn_save").click(function () {
            $("#save_status").val(1);
        })
        $(".checkbox_permission").click(function () {
            order_list_id = $(this).data('id');
            if ($(this).is(":checked")) {
                $("#product_permission_" + order_list_id).css("display", "")
                if ($(this).hasClass("red_row")) {
                    $(this).parent().parent().addClass("red_row")
                }
            } else {
                $("#product_permission_" + order_list_id).css("display", "none")
                $(this).parent().parent().removeClass("red_row")
            }
            $(".number_permission_amount").change();
        })

        $(".number_permission_amount").change(function () {
            total = 0;
            $('.number_permission_amount').each(function () {
                if ($(this).css('display') !== 'none') {
                    total += Number($(this).val()) || 0;
                    //  console.log(Number($(this).val()))
                }
            });
            // console.log(total);
            $("#number_permission_amount_sum").text(total)
        });
        $(".checkbox_permission_order").click(function () {
            order_id = $(this).data('id');
            if ($(this).is(":checked")) {
                $(".order_" + order_id).click();
                $(".order_input_" + order_id).css("display", "")
            } else {
                $(".order_" + order_id).click();
                $(".order_input_" + order_id).css("display", "none")


            }
        })

    </script>
@endsection

