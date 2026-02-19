@include("component.input._text",["id"=>"caption",'label'=>"عنوان نقص کالا ","value"=>$product_fault->caption])

<div class="col-md-12">
    @include("component.input._checkbox",["id"=>"need_to_move_shift",'label'=>"بعد اعلام نقص،  نیاز به جابجایی کارت تولید می باشد؟","checked"=>$product_fault->need_to_move_shift??false])
</div>

<div class="col-md-12">
    @include("component.input._checkbox",["id"=>"need_to_confirmation",'label'=>"بعد از اعلام نقص، آیا نیاز به تایید دارد؟","checked"=>$product_fault->need_to_confirmation??false])
</div>

    @include("component.input._radio_box01",["id"=>"product_fault_type_id",'label'=>"نوع نقص","label0"=>"نقطه ای","label1"=>"پیوسته","value0"=>2,"value1"=>1,"value"=>$product_fault->product_fault_type_id])
    @include("component.input._radio_box01",["id"=>"product_fault_fixed_type_id",'label'=>"آیا نقص رفع شدنی است؟","label1"=>"بله","label0"=>"خیر","label2"=>"مشخص نیست","value1"=>1,"value0"=>2,"value2"=>3,"value"=>$product_fault->product_fault_fixed_type_id])


@include("component.input._text",["id"=>"sms_to_posts",'label'=>"لیست پست ها جهت ارسال پیامک (هر پست را با - هم از جدا کنید.)","value"=>$product_fault->sms_to_posts])

<div class="col-md-9" data-select2-id="119">

    @include("component.input.select2._select2",[
   "id"=>"product_fault_property_ids",
   "label"=>" مشخصه(های) نقص  ",
   "option"=>$product_fault_property_id_option["items"],
   "class_col"=>""
   ])
</div>
<div class="col-md-9" data-select2-id="119">

    @include("component.input.select2._select2",[
   "id"=>"product_fault_product_fault_sign",
   "label"=>" نمود بیرونی نقص   ",
   "option"=>$product_fault_sign_option["items"],
   "class_col"=>""
   ])
</div>
