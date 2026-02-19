<div class="col-md-12">
    <div class="row">
        <div class="w-100"></div>
        <div class="col-md-6">
            <img style="width: 200px"
                 src="{{asset("chatify_app/users-avatar/".($customer->image->filename??''))}}"
                 onerror="this.onerror=null;this.src='{{url("assets/images/avatar.png")}}';"
            />
        </div>
        <div class="w-100"><br/></div>
        @if($allow_to_insert==0)
            <div class="col-md-6">
                @include("component.input._select",[
                    "id"=>"customer_type_id",
                    "label"=>" نوع مشتری   ",
                    "option"=>$customer_type_option["items"],
                    "val"=>$customer_type_option["value"],
                    "text"=>$customer_type_option["text"],
                    "class_col"=>""
                    ])

                <div class="w-100"><br/></div>
                <div class="w-100" id="customer_type1" style="display: block">


                    @include("component.input._select",[
                                   "id"=>"user_id",
                                   "label"=>"نام مشتری ",
                                   "option"=>$worker_option["items"],
                                   "val"=>$worker_option["value"],
                                   "text"=>$worker_option["text"],
                                   "class_col"=>""
                                   ])
                </div>

                <div class="w-100" id="customer_type2" style="display: none">

                    @include("component.input._select",[
                                "id"=>"company_id",
                                "label"=>" نام شرکت ",
                                "option"=>$company_option["items"],
                                "val"=>$company_option["value"],
                                "text"=>$company_option["text"],
                                "class_col"=>""
                                ])

                </div>
            </div>
        @else
            <div class="w-100"><br/></div>
            @include("component.input._lable",["id"=>"customer_type_id","label"=>"نوع مشتری","value"=>$customer->customer_type->caption??""])
            @if($customer->customer_type_id==1)
                @include("component.input._lable",["id"=>"user_id","label"=>" نام مشتری","value"=>$customer->user->fullname()??""])
            @else
                @include("component.input._lable",["id"=>"company_id","label"=>" نام شرکت  ","value"=>$customer->company->caption??""])
            @endif

        @endif

        <div class="w-100"><br/></div>
        @if($get_the_customer_image)
            @include("component.input._file_upload", ["id"=>"user_image_file_id", 'label'=>"تصویر/لوگو","value"=> ""])
        @endif

        <div class="col-md-6">
            @include("component.input._aotocomplet2",[
                "id"=>"tariff_id",
                "label"=>"  تعرفه  ",
                "option"=>$tariff_option["items"],
                "val"=>$tariff_option["value"],
                "text"=>$tariff_option["text"],
                "class_col"=>""
                ])
        </div>
        @include("component.input._text",["id"=>"code","label"=>" کد مرکز (نوسا)","value"=>$customer->code??""])
        @if($company_have_separate_financial_software)
        @include("component.input._text",["id"=>"detailed_code","label"=>" کد تفصیلی ","value"=>$customer->detailed_code??""])
        @endif
        @include("component.input._number",["id"=>"cash_off_percent","label"=>"درصد تخفیف نقدی  ","value"=>$customer->cash_off_percent??""])

        @include("component.input._number",["id"=>"percent_tax_off_in_formal_factor","label"=>"درصد تخفیف مالیات در خرید های رسمی ","value"=>$customer->percent_tax_off_in_formal_factor??""])
        @include("component.input._number",["id"=>"increase_percentage_in_informal_sale","label"=>"درصد افزایش قیمت در خرید های غیررسمی","value"=>$customer->increase_percentage_in_informal_sale??""])
        <div class="col-md-12">
            <br/>
            <input type="checkbox"
                   name="price_displayed_to_customer_with_tax" {{$customer->price_displayed_to_customer_with_tax?"checked='checked'":""}}
            >
            قیمت نمایش داده شده به مشتری با ارزش افزوده باشد؟
            <br/>
            <br/>

        </div>

        @include("component.input._number",["id"=>"percent_max_informal_purchase","label"=>"حداکثر خرید غیر رسمی (درصد)","value"=>$customer->percent_max_informal_purchase??""])
        <div class="col-md-12">
            <br/>
            <input type="checkbox"
                   name="round_fee_in_informal_sale" {{$customer->round_fee_in_informal_sale?"checked='checked'":""}}
            >
            آیا مبلغ واحد در خرید های غیر رسمی رند شود؟
            <br/>
            <br/>

        </div>

        @include("component.input._text",["id"=>"bail_amount","label"=>"میزان وثیقه ","value"=>$customer->bail_amount??"","seperated_number"=>"numeric"])


        <div class="w-100"></div>

        <div class="col-md-6">
            @include("component.input._aotocomplet2",[
                "id"=>"priority_id",
                "label"=>" اولویت سفارش   ",
                "option"=>$priority_option["items"],
                "val"=>$priority_option["value"],
                "text"=>$priority_option["text"],
                "class_col"=>""
                ])
        </div>
        <div class="w-100"></div>

        <div class="col-md-6">
            @include("component.input._aotocomplet2",[
                "id"=>"channel_id",
                "label"=>" کانال توزیع   ",
                "option"=>$channel_option["items"],
                "val"=>$channel_option["value"],
                "text"=>$channel_option["text"],
                "class_col"=>""
                ])
        </div>

        @include("component.input._text",["id"=>"sub_channel_caption","label"=>" زیر کانال توزیع    ","value"=>$customer->sub_channel_caption??"","seperated_number"=>null])


        <div class="w-100"></div>
        <div class="col-md-6">
            @include("component.input._aotocomplet2",[
                "id"=>"order_type_id",
                "label"=>"  نوع فروش  ",
                "option"=>$order_type_option["items"],
                "val"=>$order_type_option["value"],
                "text"=>$order_type_option["text"],
                "class_col"=>""
                ])
        </div>
        <div class="w-100"></div>
        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"financial_operation_pattern_id",
                "label"=>" الگوی عملیات مالی   ",
                "option"=>$financial_operation_pattern_option["items"],
                "val"=>$financial_operation_pattern_option["value"],
                "text"=>$financial_operation_pattern_option["text"],
                "class_col"=>""
                ])
        </div>

        @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
                        "id"=>"start_date_of_contract",
                        'label'=>"تاریخ شروع قرارداد ",
                        "value"=>$customer->start_date_of_contract??"",
                        ])
        <br/>
        @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
                    "id"=>"end_date_of_contract",
                    'label'=>"تاریخ پایان قرارداد ",
                    "value"=>$customer->end_date_of_contract??"",
                    ])

        <div class="col-md-12">
            <br/>
            <input type="checkbox"
                   name="get_packing_form_details" {{$customer->get_packing_form_details?"checked='checked'":""}}
            >
            در صورتی که مشتری کالای مصرفی را برای شرکت ارسال می کند،
            <br>
            آیا جزئیات بسته بندی ها از مشتری دریافت گردد؟


        </div>
        <div class="col-md-12">
            <br/>
            <input type="checkbox"
                   name="send_order_sms" {{$customer->send_order_sms?"checked='checked'":""}}
            >
           آیا پیامک های ثبت سفارش  برای مشتری ارسال گردد؟


        </div>
        <div class="col-md-12">
            <br/>
            <input type="checkbox"
                   name="send_exit_form_sms" {{$customer->send_exit_form_sms?"checked='checked'":""}}
            >
           آیا پیامک های برگ خروج  برای مشتری ارسال گردد؟


        </div>
        <div class="col-md-12">
            <br/>
            <input type="checkbox"
                   name="send_register_sms" {{$customer->send_register_sms?"checked='checked'":""}}
            >
           آیا پیامک های ثبت نام  برای مشتری ارسال گردد؟
            <br/>
            <br/>

        </div>
        <div class="col-md-12">
            <br/>
            <input type="checkbox"
                   name="payment_terms_display_in_per_factor" {{$customer->payment_terms_display_in_per_factor?"checked='checked'":""}}
            >
            آیا شرایط پرداخت در پیش فاکتور نمایش داده شود؟
            <br/>
            <br/>

        </div>

        @include("component.input._textarea",["id"=>"payment_terms","label"=>"توضیحات اختصاصی مشتری","value"=>$customer->payment_terms??"","seperated_number"=>null])

    </div>

</div>

{{--<div class="col-md-12">--}}

{{--    <div class="row">--}}
{{--        <h5> تغییر کلمه عبور</h5>--}}
{{--        @include("component.input._text",["id"=>"email","label"=>"نام کاربری  ","value"=>$customer->user->email??""])--}}

{{--        <div class="col-md-6 offset-md-6">--}}
{{--            <div class="form-group">--}}
{{--                <label>کلمه عبور جدید </label>--}}
{{--                <input name="password" id="password" value="" type="password"--}}
{{--                       class="form-control" autocomplete="new-password">--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-md-6 offset-md-6">--}}
{{--            <div class="form-group">--}}
{{--                <label> تکرار کلمه عبور </label>--}}
{{--                <input name="confirm_password" id="confirm_password" value="" type="password" class="form-control">--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}


{{--</div>--}}






