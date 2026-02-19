@include("component.input._lable",["id"=>"cash_off_percent","label"=>"درصد تخفیف نقدی  ","value"=>$employment->customer->cash_off_percent??""])

@include("component.input._lable",["id"=>"increase_percentage_in_informal_sale","label"=>"درصد افزایش قیمت در خرید های غیررسمی","value"=>$employment->customer->increase_percentage_in_informal_sale??""])

@include("component.input._lable",["id"=>"price_displayed_to_customer_with_tax","label"=>"آیا  قیمت نمایش داده شده به مشتری با ارزش افزوده باشد؟ ","value"=>$employment->customer->price_displayed_to_customer_with_tax?"بله":"خیر"])
@include("component.input._lable",["id"=>"percent_max_informal_purchase","label"=>"حداکثر خرید غیر رسمی (درصد)","value"=>$employment->customer->percent_max_informal_purchase??""])
@include("component.input._lable",["id"=>"round_fee_in_informal_sale","label"=>"    آیا مبلغ واحد در خرید های غیر رسمی رند شود؟ ","value"=>$employment->customer->round_fee_in_informal_sale?"بله":"خیر"])
@include("component.input._lable",["id"=>"bail_amount","label"=>"میزان وثیقه ","value"=>$employment->customer->bail_amount??""])


<div class="w-100"></div>

<div class="col-md-6">
    @include("component.input._lable",[
        "id"=>"priority_id",
        "label"=>" اولویت سفارش   ",
        "value"=>$employment->customer->priority->caption??"",
        "class_col"=>""
        ])
</div>
<div class="w-100"></div>

<div class="col-md-6">
    @include("component.input._lable",[
        "id"=>"channel_id",
        "label"=>" کانال توزیع   ",
        "value"=>$employment->customer->channel->caption??"",
        "class_col"=>""
        ])
</div>

@include("component.input._lable",["id"=>"sub_channel_caption","label"=>" زیر کانال توزیع ", "value"=>$employment->customer->sub_channel_caption??""])

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._lable",[
        "id"=>"tariff_id",
        "label"=>"  تعرفه  ",
        "value"=>$employment->customer->tariff->caption??"",
        "class_col"=>"",

        ])
</div>
<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._lable",[
        "id"=>"order_type_id",
        "label"=>"  نوع فروش  ",
        "value"=>$employment->customer->order_type->caption??"",
        "class_col"=>""
        ])
</div>
<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._lable",[
        "id"=>"financial_operation_pattern_id",
        "label"=>" الگوی عملیات مالی   ",
        "value"=>$employment->customer->financial_operation_pattern->caption??"",
        "class_col"=>""
        ])
</div>

<div class="col-md-6">
    @include("component.input._lable",[
        "id"=>"end_date_of_contract",
        "label"=>" پایان قرارداد ",
        "value"=>$employment->customer->get_end_date_of_contract()??"",
        "class_col"=>""
        ])
</div>
@include("component.input._lable",["id"=>"send_order_sms","label"=>" آیا پیامک های ثبت سفارش  برای مشتری ارسال گردد؟ ","value"=>$employment->customer->send_order_sms?"بله":"خیر"])
@include("component.input._lable",["id"=>"send_exit_form_sms","label"=>" آیا پیامک های برگ خروج  برای مشتری ارسال گردد؟","value"=>$employment->customer->send_exit_form_sms?"بله":"خیر"])
@include("component.input._lable",["id"=>"round_fee_in_informal_sale","label"=>"آیا پیامک های ثبت نام  برای مشتری ارسال گردد؟","value"=>$employment->customer->send_register_sms?"بله":"خیر"])
@include("component.input._lable",["id"=>"round_fee_in_informal_sale","label"=>"     آیا شرایط پرداخت در پیش فاکتور نمایش داده شود؟ ","value"=>$employment->customer->payment_terms_display_in_per_factor?"بله":"خیر"])
<div class="col-md-6">
   @if($employment->customer->payment_terms)
       شرایط پرداخت:
       {!! $employment->customer->payment_terms   !!}
   @endif
</div>