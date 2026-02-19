<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5>مشخصات روش پرداخت </h5>
        </div>
        <div class="card-block">
            <div class="row">


                @include("component.input._lable",["id"=>"prepayment_amount", "lable"=>"نوع فروش","value"=>$order->selling_type->caption??"","class_col"=>"col-md-12"])




                @foreach($order->order_payment_method as $item)
                    @include("component.input._lable",["id"=>"cash_amount", "lable"=>"مبلغ ".$item->payment_method_type->caption,"value"=>number_format($item->amount)." ".($order->customer->tariff->currency->caption??""),"class_col"=>"col-md-12"])

                @endforeach

                    @include("component.input._lable",["id"=>"prepayment_amount", "lable"=>"راس چک ها","value"=>($order->check_delivery_days??"")." روز","class_col"=>"col-md-12"])


            </div>
        </div>
    </div>
</div>
