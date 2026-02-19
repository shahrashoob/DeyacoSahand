@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")
    <div class="row">

        @include("customer.group.buy._order_factor_products")
    </div>

    <form id="form1" style="display: inline"
          action="{{route("customer_group.buy.payment_method_step1_submit",$order)}}" method="post"
          autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>مشخصات روش پرداخت</h5>
                    </div>
                    <div class="card-block overflow-auto">
                        @if($order->customer->cash_off_percent > 0)
                            <div class="alert alert-success ">

                                در صورت پرداخت نقدی از {{$order->customer->cash_off_percent}} درصد تخفیف برخوردار خواهید
                                شد.
                            </div>

                        @endif


                        @include("component.input.datepicker._datepicker",["id"=>"delivery_datetime",
"lable"=>" تاریخ تحویل  سفارش  ","value"=>$order->delivery_datetime??null,
"class_col"=>"col-md-3"])
                        <div class="w-100"></div>

                        @foreach($customer_payment_method as $item)




                            @include("component.input._number",[
	                            "id"=>"payment_method_".$item->payment_method_type_id,
	                            "lable"=>"مبلغ ".$item->caption." (".$item->min_percentage."% - ".$item->max_percentage."%)",
	                            "value"=>$order_payment_method_value[$item->payment_method_type_id],
	                            "class_col"=>"col-md-12 class_payment",
	                            "other_content"=>"<input type='number' class='input_percent' data-id='".$item->payment_method_type_id."' value='".(round($order_payment_method_value[$item->payment_method_type_id]/$sum_order_factor_with_tax*100))."'> <i class='fa fa-percent'></i>"
	                            ])


                            <div class="w-100"></div>

                        @endforeach

                        <input type="hidden" id="sum_all" value="{{$sum_order_factor_with_tax}}">
                        <div class="col-md-12 center">
                            <a class="btn btn-dark"
                               href="{{route("customer_group.buy.address",$order)}}">بازگشت</a>
                            <button type="submit" class="btn btn-primary">ثبت و ادامه</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        .form-control {
            display: inline;
        }

        .class_payment .input_percent {
            width: 40px !important;
        }

        .class_payment input {
            width: 160px;
        }
#delivery_datetime{
    width: 90px;
}
    </style>
@endsection
@section("scripts")
    <script>
        $('#form1').validate({

            rules: {
                @foreach($customer_payment_method as $item)
                "payment_method_{{$item->payment_method_type_id}}": {
                    required: true,
                    min: {{round($item->min_percentage/100 * $sum_order_factor_with_tax)}},
                    max: {{round($item->max_percentage/100 * $sum_order_factor_with_tax)}},
                },
                @endforeach

                "delivery_datetime_value": "required",
            }
        });
        $("input").removeClass("form-control");
        $(".input_percent").change(function () {
            $value = Math.round($("#sum_all").val() * $(this).val() / 100, 2);
            $("#payment_method_" + $(this).data("id")).val($value)
        })
    </script>
@endsection

