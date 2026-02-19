@include("component.input._text",["id"=>"caption",'label'=>"عنوان عیب ماشین ","value"=>$machine_fault->caption])

@include("component.input._text",["id"=>"sms_to_posts",'label'=>"لیست پست ها جهت ارسال پیامک (هر پست را با - هم از جدا کنید.)","value"=>$machine_fault->sms_to_posts])

<div class="col-md-12">
    @include("component.input._checkbox",["id"=>"need_to_confirmation",'label'=>"بعد از اعلام نقص، آیا نیاز به تایید دارد؟","checked"=>$machine_fault->need_to_confirmation??false])
    @include("component.input._checkbox",["id"=>"need_to_confirmation_for_fix",'label'=>"بعد از رفع نقص، آیا نیاز به تایید دارد؟","checked"=>$machine_fault->need_to_confirmation_for_fix??false])
</div>

<div class="col-md-9" data-select2-id="119">

    @include("component.input.select2._select2",[
   "id"=>"machine_fault_machine_fault_sign",
   "label"=>" نمود بیرونی نقص   ",
   "option"=>$machine_fault_sign_option["items"],
   "class_col"=>""
   ])
</div>

{{-- <div class="col-md-9" data-select2-id="119">

    @include("component.input.select2._select2",[
   "id"=>"machine_fault_machine_fault_sign",
   "label"=>"تایید کننده نقص ",
   "option"=>$machine_fault_confirm_post["items"],
   "class_col"=>""
   ])
</div> --}}
