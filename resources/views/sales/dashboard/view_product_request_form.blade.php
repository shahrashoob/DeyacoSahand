@extends('layouts.admin._master')
@section("page_header_title","داشبورد فروش - سفارش   ".$order->code())

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>فرم درخواست کالا از انبار کد {{$product_request_form->code}}</h5>
                </div>
                <div class="card-block">


                    <div class="row">
                        @include("warehouse.out.dashboard._small_info")

                        <div class="table-responsive">
                            <table class="table table-styling center" style="">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th></th>
                                    <th>کد کالا</th>
                                    <th> عنوان کالا</th>
                                    <th> واحد سنجش</th>
                                    <th> خط ورودی</th>
                                    <th> مقدار درخواست</th>
                                    <th> مقدار تحویل شده</th>
                                    <th> مقدار باقی مانده</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0; $allow_for_request=0; @endphp
                                @foreach($product_request_form->items as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            @if($item->amount_remaining> 0 )
                                                @php $allow_for_request=1;@endphp
                                                <a class="text-danger"
                                                   href="{{route('sales.product_request_permission.remove_product_request_form_item',[$product_request_form,$item->product,"back_to_order"])}}"
                                                   onclick="return confirm('در صورت کنسل کردن،  مقدار  مجوز برابر می شود با: \n جمع مقدار ارسال شده  و مقدار در حال تحویل ')">
                                                    <i class="fas fa-level-down-alt"></i>
                                                    <i style="margin-right: -5px" class="fas fa-shopping-basket"></i>

                                                </a>

                                            @endif
                                        </td>
                                        <td>{{$item->product->code}}</td>
                                        <td>{{$item->product->caption}}</td>
                                        <td>{{$item->product->unit->caption}}</td>
                                        <td>{{$item->input_line_code}}</td>
                                        <td>{{$item->amount_request}}</td>
                                        <td>
                                            {{$item->amount_sent}}

                                        </td>
                                        <td>{{$item->amount_remaining}}

                                        </td>


                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>


                    </div>
                    <div class="center">
                        @switch($back_type)
                            @case ("back_to_permission")

                                <a class="btn btn-outline-dark"
                                   href="{{route("sales.product_request_permission.get_other_customer_permission",[$q1,$q2])}}">

                                    بازگشت

                                </a>
                                @break
                            @default
                                <a class="btn btn-outline-dark"
                                   href="{{route("sales.dashboard.view_order",[$order])}}">

                                    بازگشت

                                </a>
                        @endswitch

                        @if($allow_for_request || $product_request_form->status_id == 7005001)
                            <a class="btn btn-danger"
                               href="{{route('sales.product_request_permission.remove_product_request_form',[$product_request_form,0,"back_to_order"])}}"
                               onclick="return confirm('در صورت کنسل کردن،  مقدار  مجوز برابر می شود با: \n جمع مقدار ارسال شده  و مقدار در حال تحویل ')">
                                <i class="fas fa-level-down-alt"></i>
                                <i style="margin-right: -5px" class="fas fa-shopping-basket"></i>

                                تعدیل کل درخواست به مقدار ارسال شده

                            </a>
                        @endif


                    </div>
                </div>
            </div>
        </div>


    </div>

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "packing_type_id_auto": "required",
                "degree_id_auto": "required",
                "amount": {
                    required: true,
                    min: 1
                },
                "carrier_code": "required"
            }
        });
    </script>
@endsection
