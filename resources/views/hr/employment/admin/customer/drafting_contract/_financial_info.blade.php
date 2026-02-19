<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"tariff_id",
        "label"=>"  تعرفه  ",
        "option"=>$tariff_option["items"],
        "val"=>$tariff_option["value"],
        "text"=>$tariff_option["text"],
        "class_col"=>"",
        'mark'=>'*',
        "url"=>route("hr.employment.admin.customer.tariff.create",$employment),
        "url_text"=>" <i class='fa fa-plus'></i> "." "." تعرفه جدید ",
        "target"=>"_self"
        ])
</div>
@include("component.input._number",["id"=>"cash_off_percent","label"=>"درصد تخفیف نقدی  ","value"=>"",'mark'=>'*'])

@include("component.input._number",["id"=>"percent_tax_off_in_formal_factor","label"=>"درصد تخفیف مالیات در خرید های رسمی ","value"=>"",'mark'=>'*'])
@include("component.input._number",["id"=>"increase_percentage_in_informal_sale","label"=>"درصد افزایش قیمت در خرید های غیررسمی","value"=>"",'mark'=>'*'])

<div class="col-md-12">
    <br/>
    <input type="checkbox"
           name="price_displayed_to_customer_with_tax"
    >
    قیمت نمایش داده شده به مشتری با ارزش افزوده باشد؟
    <br/>
    <br/>

</div>

@include("component.input._number",["id"=>"percent_max_informal_purchase","label"=>"حداکثر خرید غیر رسمی (درصد)","value"=>"",'mark'=>'*'])
<div class="col-md-12">
    <br/>
    <input type="checkbox"
           name="round_fee_in_informal_sale"
    >
    آیا مبلغ واحد در خرید های غیر رسمی رند شود؟
    <br/>
    <br/>

</div>
@include("component.input._text",["id"=>"bail_amount","label"=>"میزان وثیقه ","value"=>"","seperated_number"=>"numeric",'mark'=>'*'])


<div class="w-100"></div>

<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"priority_id",
        "label"=>" اولویت سفارش   ",
        "option"=>$priority_option["items"],
        "val"=>$priority_option["value"],
        "text"=>$priority_option["text"],
        "class_col"=>"",
        'mark'=>'*'
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
        ,'mark'=>'*'
        ])
</div>

@include("component.input._text",["id"=>"sub_channel_caption","label"=>" زیر کانال توزیع    ","value"=>""])


<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"order_type_id",
        "label"=>"  نوع فروش  ",
        "option"=>$order_type_option["items"],
        "val"=>$order_type_option["value"],
        "text"=>$order_type_option["text"],
        'mark'=>'*',
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
        'mark'=>'*',
        "class_col"=>""
        ])
</div>
<br/>
@include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
            "id"=>"end_date_of_contract",
            'label'=>"تاریخ پایان قرارداد ",
            'mark'=>'*',
            "value"=>"",
            ])

<div class="col-md-12">
    <br/>
    <input type="checkbox"
           name="get_packing_form_details"
    >
    در صورتی که مشتری کالای مصرفی را برای شرکت ارسال می کند،
    <br>
    آیا جزئیات بسته بندی ها از مشتری دریافت گردد؟


</div>
<div class="col-md-12">
    <br/>
    <input type="checkbox"
           name="send_order_sms" {{$send_order_sms?"checked='checked'":""}}
    >
    آیا پیامک های ثبت سفارش  برای مشتری ارسال گردد؟


</div>

<div class="col-md-12">
    <br/>
    <input type="checkbox"
           name="send_exit_form_sms" {{$send_exit_form_sms?"checked='checked'":""}}
    >
    آیا پیامک های برگ خروج  برای مشتری ارسال گردد؟


</div>
<div class="col-md-12">
    <br/>
    <input type="checkbox"
           name="send_register_sms" {{$send_register_sms?"checked='checked'":""}}
    >
    آیا پیامک های ثبت نام  برای مشتری ارسال گردد؟
    <br/>
    <br/>

</div>
<div class="col-md-12">
    <br/>
    <input type="checkbox"
           name="payment_terms_display_in_per_factor"
    >
    آیا شرایط پرداخت در پیش فاکتور نمایش داده شود؟
    <br/>
    <br/>

</div>
@include("component.input._textarea",["id"=>"payment_terms","label"=>"توضیحات اختصاصی مشتری","value"=>"","seperated_number"=>null])

