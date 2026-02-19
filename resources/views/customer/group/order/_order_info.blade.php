<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5>مشخصات سفارش</h5>
        </div>
        <div class="card-block">
            <div class="row">
                @include("component.input._lable",["id"=>"","lable"=>" کد سفارش  ","value"=>$order->code(),"class_col"=>"col-md-4"])



                @include("component.input._lable",["id"=>"","lable"=>" نام مرکز  ",
                                "value"=>$order->customer->caption??"","class_col"=>"col-md-4"])

                @include("component.input._lable",["id"=>"","lable"=>" کد مرکز  ",
                                "value"=>$order->customer->code??"","class_col"=>"col-md-4"])



                @include("component.input._lable",["id"=>"","lable"=>" تاریخ ایجاد پیش نویس   ",
                                              "value"=>$order->create_datetime(),"class_col"=>"col-md-4"])

                @include("component.input._lable",["id"=>"","lable"=>" تاریخ ثبت درخواست   ",
                                "value"=>$order->order_datetime(),"class_col"=>"col-md-4"])

                @include("component.input._lable",["id"=>"prepayment_amount", "lable"=>"تاریخ تحویل","value"=>$order->delivery_datetime(),"class_col"=>"col-md-4"])




                @include("component.input._lable",["id"=>"","lable"=>" وزن کل     ",
                "value"=>($order->total_weight??"0")." کیلوگرم","class_col"=>"col-md-4"])

                @include("component.input._lable",["id"=>"","lable"=>" وزن بار ارسال نشده     ",
                "value"=>($order->total_weight_remaining??"0")." کیلوگرم","class_col"=>"col-md-4"])

                @include("component.input._lable",["id"=>"","lable"=>" نسبت وزنی ارسال شده     ",
                "value"=>($order->total_weight_sent_raito??"0" ). "%","class_col"=>"col-md-4"])




                @include("component.input._lable",["id"=>"","lable"=>" وضعیت    ",
                "value"=>$order->getStatus(1)??"","class_col"=>"col-md-4"])



                @include("component.input._lable",["id"=>"","lable"=>" شرح برگه    ",
                "value"=>$order->description_sheet->text??""])

            </div>
        </div>
    </div>
</div>
