@if($order->shipping_method)
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>مشخصات ارسال بار </h5>
            </div>
            <div class="card-block">
                <div class="row">

                    @include("component.input._lable",["id"=>"","lable"=>"روش ارسال بار",
                    "value"=>$order->shipping_method->caption??"","class_col"=>"col-md-3"])

                    @include("component.input._lable",["id"=>"","lable"=>"نوع خودرو",
                    "value"=>$order->car_type->caption??"","class_col"=>"col-md-3"])

                    @include("component.input._lable",["id"=>"","lable"=>"محل تحویل کالا ",
                    "value"=>$order->delivery_point_type->caption,"class_col"=>"col-md-3"])

                    @include("component.input._lable",["id"=>"","lable"=>"ارزش بیمه نامه",
                    "value"=>number_format($order->insurance_amount)." ریال ","class_col"=>"col-md-3"])

                    @include("component.input._lable",["id"=>"","lable"=>"هزینه ارسال بار",
                    "value"=>number_format($order->shipping_cost)." ریال ","class_col"=>"col-md-3"])


                </div>
            </div>
        </div>
    </div>
@endif