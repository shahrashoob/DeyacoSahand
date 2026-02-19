<div class="card">
    <div class="card-header">
        <h5>اطلاعات برنامه ریزی</h5>
    </div>
    <div class="card-block">
        <div class="row">


            <div class="col-md-6">

                <div class="row">
                    @include("component.input._lable",[
                        "label"=>"  موجودی فعلی کالا ",
                        "value"=>$product_inventory." ".$order_list->product->unit->caption,

                        ])

                    @include("component.input._lable",[
                       "label"=>"  مقدار بسته بندی های در راه ",
                       "value"=>$sum_packing_forms." ".$order_list->product->unit->caption,

                       ])

                    @include("component.input._lable",[
                       "label"=>"  مقدار کارت های تولید و تخصیص ها ",
                       "value"=>$sum_production." ".$order_list->product->unit->caption,

                       ])


                </div>

            </div>
            <div class="col-md-6">

                <div class="row">
                    @include("component.input._lable",[
                        "label"=>"  مقدار این سفارش",
                        "value"=>$order_list->amount." ".$order_list->product->unit->caption,

                        ])


                </div>
                <div class="row">
                    @include("component.input._lable",[
                        "label"=>"  مقدار سایر سفارش ها",
                        "value"=>$amount_of_other_sales." ".$order_list->product->unit->caption,

                        ])


                </div>
                <div class="row">
                    @include("component.input._lable",[
                        "label"=>"حداقل موجودی انبار (نقطه سفارش)",
                        "value"=>$order_list->product->min_inventory." ".$order_list->product->unit->caption,

                        ])


                </div>

            </div>

        </div>

    </div>
</div>