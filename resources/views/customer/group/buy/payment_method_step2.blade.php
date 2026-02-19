@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")

    <div class="row">

        @include("customer.group.buy._order_factor_products")
    </div>
    <form id="form1" style="display: inline"
          action="{{route("customer_group.buy.payment_method_step2_submit",$order)}}" method="post"
          autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>مشخصات روش پرداخت (اعتباری)</h5>
                    </div>
                    <div class="card-block overflow-auto">

                        <div class="row ">
                            @foreach($order->order_payment_method as $item)

                                @if($max_days[$item->payment_method_type_id] > 0 && $item->amount > 0)
                                    @include("component.input._number",[
                                          "id"=>"check_delivery_days_".$item->id,
                                          "label"=>"راس چک ها برای ".$item->payment_method_type->caption." (حداکثر  ".$max_days[$item->payment_method_type_id]." روز می باشد)",
                                          "value"=>$item->check_delivery_days,
                                          "text"=>$order->check_delivery_days,
                                          "class_col"=>"col-md-3 col-sm-6"
                                          ])
                                    @include("component.input._lable",["id"=>"cash_amount", "lable"=>"مبلغ ","value"=>number_format($item->amount)." ".($order->customer->tariff->currency->caption??""),"class_col"=>"col-md-6"])
                                @endif
                                <div class="w-100"></div>
                            @endforeach


                            <div class="col-md-12 center">
                                <a class="btn btn-dark"
                                   href="{{route("customer_group.buy.payment_method_step1",$order)}}">بازگشت</a>
                                <button type="submit" class="btn btn-primary">ثبت و ادامه</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </form>





@endsection

@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                @foreach($order->order_payment_method as $item)
                "check_delivery_days_{{$item->id}}": {
                    required: true,
                    max: {{$max_days[$item->payment_method_type_id]}},
                    min: 1
                },
                @endforeach
            }
        });


    </script>
@endsection

