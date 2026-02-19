@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")

    <div class="row">

        @include("customer.group.buy._order_consumed_products")
        @include("customer.group.buy._order_factor_products")

        @include("customer.group.buy._order_factor_customer_info")
        @include("sales.dashboard._order_packing_form")

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>مشخصات روش پرداخت (اعتباری)</h5>
                </div>
                <div class="card-block overflow-auto">

                    <div class="row ">

                        @include("component.input._lable",["id"=>"prepayment_amount", "lable"=>"نوع فروش","value"=>$order->selling_type->caption,"class_col"=>"col-md-6"])
                        <div class="w-100"></div>
                        @include("component.input._lable",["id"=>"prepayment_amount", "lable"=>"تاریخ تحویل سفارش ","value"=>$order->delivery_datetime(),"class_col"=>"col-md-6"])
                        <div class="w-100"></div>


                        @foreach($order->order_payment_method as $item)
                            @include("component.input._lable",[
                                "id"=>"cash_amount",
                                 "lable"=>"مبلغ ".$item->payment_method_type->caption,
                                 "value"=>$item->amount." ".($order->customer->tariff->currency->caption??"").($item->check_delivery_days>0?" (راس چک ها ".$item->check_delivery_days." روز)":""),
                                 "class_col"=>"col-md-6"
	 ])
                            <div class="w-100"></div>
                        @endforeach

                    </div>

                </div>
            </div>
        </div>


        <div class="col-md-12">
            <form id="form1" style="display: inline"
                  action="{{route("customer_group.buy.shopping_cart_confirm",$order)}}" method="post"
                  autocomplete="off">
                @csrf

                @if($allow_get_shipping_method_in_buy)
                    @include("customer.group.buy.shipping_method",["insurance_amount"=>$order->insurance_amount,"shipping_cost"=>$order->shipping_cost])
                @endif

                <div class="w-100"></div>
                <br/>
                <br/>
                <div class="center">
                    <a class="btn btn-dark btn-lg"
                       href="{{route("customer_group.buy.payment_method_step1",$order)}}">بازگشت</a>
                    <button type="submit" class="btn btn-success btn-lg" id="btn_other"
                    > تایید سفارش
                    </button>
                </div>
            </form>
        </div>


    </div>

@endsection

@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    @include("component.input._seperated_number_3")
    <script>
        $('#form1').submit(function () {
            if ($("#payment_method_id_auto").val() != "")
                return confirm('آیا از ثبت و تایید سفارش اطمینان دارید؟');
        })
        $('#form1').validate({
            rules: {
                delivery_datetime_value: "required",
                shipping_method_id: "required",
                shipping_cost: "required",
                insurance_amount: "required",
                car_type_id: "required",
                postal_code: {minlength: 10, maxlength: 10},
            }
        });
    </script>

@endsection
