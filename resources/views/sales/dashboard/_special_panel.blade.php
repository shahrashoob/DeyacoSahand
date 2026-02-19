<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5>مشخصات مهم سفارش</h5>
        </div>
        <div class="card-block">
            <div class="row">


                @include("component.input._lable",["id"=>"","lable"=>" نام مرکز  ",
                                "value"=>$order->customer->caption??"","class_col"=>"col-md-3"])

                @include("component.input._lable",["id"=>"","lable"=>($order->unit->measurement??"مقدار")."  کل ",
                "value"=>($order->total??"0")." ".($order->unit->caption??"مقدار"),"class_col"=>"col-md-3"])


                @include("component.input._lable",["id"=>"","lable"=>" مبلغ کل فاکتور   ",
                "value"=>number_format($order->orderFactor()->sum("total_price_with_tax"))." ".($order->customer->tariff->currency->caption??""),"class_col"=>"col-md-4"])





            </div>
        </div>
    </div>
</div>
