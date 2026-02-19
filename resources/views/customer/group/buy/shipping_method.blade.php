<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5>مشخصات روش ارسال بار</h5>
        </div>
        <div class="card-block overflow-auto">

            <div class="row ">
                @if(isset($with_delivery_datetime))
                    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"delivery_datetime",
    "lable"=>" حداکثر تاریخ ارسال بار ","value"=>null,
    "class_col"=>"col-md-3"])
                @endif

                <div class="w-100"></div>
                @include("component.input._select",[
                                                   "id"=>"shipping_method_id",
                                                   "label"=>"روش ارسال بار",
                                                   "option"=>$shipping_method_option["items"],
                                                   "val"=>$shipping_method_option["value"],
                                                   "text"=>$shipping_method_option["text"],
                                                   "class_col"=>"col-md-3"
                                                   ])
                <div class="w-100"><br/></div>
                @include("component.input._select",[
                                                   "id"=>"car_type_id",
                                                   "label"=>"نوع خودرو ",
                                                   "option"=>$car_type_option["items"],
                                                   "val"=>$car_type_option["value"],
                                                   "text"=>$car_type_option["text"],
                                                   "class_col"=>"col-md-3"
                                                   ])

                <div class="w-100"><br/></div>
                @include("component.input._select",[
                                                   "id"=>"delivery_point_type_id",
                                                   "label"=>"محل تحویل کالا ",
                                                   "option"=>$delivery_point_type_option["items"],
                                                   "val"=>$delivery_point_type_option["value"],
                                                   "text"=>$delivery_point_type_option["text"],
                                                   "class_col"=>"col-md-3"
                                                   ])

                <div class="w-100"><br/></div>
                @include("component.input._text",["id"=>"insurance_amount",'label'=>" ارزش  بیمه نامه (ریال)","value"=>$insurance_amount??"","class_col"=>"col-md-3 ","seperated_number"=>"numeric"])
                <div class="w-100"></div>
                @include("component.input._text",["id"=>"shipping_cost",'label'=>" هزینه ارسال بار (ریال)","value"=>$shipping_cost??"","class_col"=>"col-md-3 ","seperated_number"=>"numeric"])


            </div>

        </div>
    </div>
</div>