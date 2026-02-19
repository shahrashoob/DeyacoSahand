@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  فروش "." سفارش:".$order->code())

@section('content')

    <form id="form1" autocomplete="off"
          action="{{route("sales.product_request_permission.confirm",[$order])}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> ثبت مجوز بارگیری برای {{$order->customer->caption}} </h5>
                    </div>

                    <div class="card-block">

                        <div class="row">


                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-styling center" style="">
                                        <thead>
                                        <tr>

                                            <th>ردیف</th>
                                            <th>شماره سفارش</th>
                                            <th>نام و کد کالا</th>
                                            <th> واحد سنجش</th>
                                            <th> سفارش</th>
                                            <th> مجوز خروج جدید</th>

                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $row=0; $sum_order=0; $sum_permission=0@endphp
                                        @foreach($product_request_form_items as $item)
                                            @php
                                                $order_amount=is_null($item->order_amount)?0:$item->order_amount;
                                                  $new_permission_amount=$new_permission_list[$item->order_id][$item->order_list_id];
                                                    $sum_order+=$order_amount;
                                                    $sum_permission+=$new_permission_amount;
                                            @endphp
                                            <tr>
                                                <td>{{++$row}}</td>
                                                <td>{{$item->order_series."/".$item->order_code}}</td>
                                                <td>{{$item->product->caption}} <br/>
                                                    {{$item->product->code}}
                                                </td>
                                                <td>{{$item->product->unit->caption}}</td>

                                                <td>{{$order_amount}}</td>
                                                <td>
                                                    {{$new_permission_amount}}
                                                </td>

                                            </tr>

                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td colspan="2">جمع کل</td>

                                            <td>{{$sum_order}}</td>
                                            <td>
                                                {{$sum_permission}}
                                            </td>

                                        </tr>
                                        </tbody>
                                    </table>

                                </div>


                            </div>


                            @if($allow_get_shipping_method_in_product_permission)

                                @include("customer.group.buy.shipping_method",["insurance_amount"=>$order->insurance_amount,"shipping_cost"=>$order->shipping_cost,"with_delivery_datetime"=>1])
                            @else
                                @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"delivery_datetime",
"lable"=>" حداکثر تاریخ ارسال بار ","value"=>null,
"class_col"=>"col-md-3"])
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>اطلاعات آدرس و تماس تحویل گیرنده بار</h5>

                    </div>
                    <div class="card-block overflow-auto">


                        <div class="row">
                            @include("customer.public._address_item_input",["address"=>$order->address,"route_path"=>"sales.product_request_permission.address_edit"])
                            <div class="w-100"><br/></div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12 center">
                <br/>
                <a href="{{route("sales.product_request_permission.index",$order)}}" class="btn btn-outline-dark     "
                   type="button">
                    بازگشت
                </a>
                <button
                        class="btn btn-success" type="submit">
                    تایید نهایی مجوز بارگیری
                    (دستور ارسال بار)
                </button>
            </div>
        </div>
    </form>
@endsection


@section("scripts")
    @include("component.input._seperated_number_3")
    @include("component.input.datepicker.jalali_datepicker._script")
    <script>
        $('#form1').validate({
            rules: {
                delivery_datetime_value: "required",
                shipping_method_id: "required",
                shipping_cost: "required",
                insurance_amount: "required",
                car_type_id: "required",
                mobile: {minlength: 10, maxlength: 10},
                phone: {minlength: 11, maxlength: 11},
                postal_code: {minlength: 10, maxlength: 10},
            }
        });
        $(".checkbox_permission").click(function () {
            order_list_id = $(this).data('id');
            if ($(this).is(":checked")) {
                $("#product_permission_" + order_list_id).css("display", "")
            } else {
                $("#product_permission_" + order_list_id).css("display", "none")
            }
        })

    </script>
    @include("customer.group.buy._address_script")
@endsection

@section("styles")

    @include("component.input.datepicker.jalali_datepicker._style")



    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    @endsection

