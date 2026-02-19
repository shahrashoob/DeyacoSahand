<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5>مشخصات مالی </h5>
        </div>
        <div class="card-block">
            <div class="row">

                @include("component.input._lable",["id"=>"","lable"=>" کد تفصیلی  ",
                                "value"=>$order->customer->detailed_code??"","class_col"=>"col-md-3"])

                @include("component.input._lable",["id"=>"prepayment_amount", "lable"=>"نوع فروش","value"=>$order->selling_type->caption??"","class_col"=>"col-md-3"])


                @if(isset($account_balances))
                    @include("component.input._lable",["id"=>"","lable"=>"مانده حساب های دریافتنی",
                    "value"=>$account_balances->getBalance1401(),"class_col"=>"col-md-3"])

                    @include("component.input._lable",["id"=>"","lable"=>"اسناد نزد صندوق",
                    "value"=>$account_balances->getBalance1301(),"class_col"=>"col-md-3"])

                    @include("component.input._lable",["id"=>"","lable"=>"اسناد در جریان وصول ",
                    "value"=>$account_balances->getBalance1302(),"class_col"=>"col-md-3"])

                    @include("component.input._lable",["id"=>"","lable"=>"اسناد برگشتنی ",
                    "value"=>$account_balances->getBalance1303(),"class_col"=>"col-md-3"])



                @else
                    @include("component.input._lable",["id"=>"","lable"=>"مانده حساب های دریافتنی",
                   "value"=>0,"class_col"=>"col-md-3"])

                    @include("component.input._lable",["id"=>"","lable"=>"اسناد نزد صندوق",
                    "value"=>0,"class_col"=>"col-md-3"])

                    @include("component.input._lable",["id"=>"","lable"=>"اسناد در جریان وصول ",
                    "value"=>0,"class_col"=>"col-md-3"])

                    @include("component.input._lable",["id"=>"","lable"=>"اسناد برگشتنی ",
                    "value"=>0,"class_col"=>"col-md-3"])
                @endif

                @php $order_price=$order->customer->getAllSellingAmount();@endphp
                @if(isset($order_price["selling_type"]))
                    @foreach($order_price["selling_type"] as $item)
                        @include("component.input._lable",["id"=>"","lable"=>"مجموع فروش ".$item["caption"],
                        "value"=>number_format($item["total_price_with_tax"])." ".($order->customer->tariff->currency->caption??"")."(".($item["percent"])."%)","class_col"=>"col-md-3"])
                    @endforeach
                @endif

                @if(isset($order_price["status_item"]))
                    @foreach($order_price["status_item"] as $item)
                        @include("component.input._lable",["id"=>"","lable"=>"مجموع سفارش های ".$item["caption"],
                        "value"=>number_format($item["total_price_with_tax"])." ".($order->customer->tariff->currency->caption??""),"class_col"=>"col-md-6"])
                    @endforeach
                @endif


                @foreach($order->order_payment_method as $item)
                    @include("component.input._lable",["id"=>"cash_amount", "lable"=>"مبلغ ".$item->payment_method_type->caption,
                        "value"=>number_format($item->amount)." ".($order->customer->tariff->currency->caption??"").($item->check_delivery_days>0?" (راس چک ها ".$item->check_delivery_days." روز)":""),
                        "class_col"=>"col-md-3"])

                @endforeach


            </div>
        </div>
    </div>
</div>
