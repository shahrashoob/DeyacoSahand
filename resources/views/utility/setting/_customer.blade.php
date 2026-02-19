<form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="row">

        @include("component.input._radio_box01",["id"=>$values["customer_draft_contract_confirm"]->key,"label"=>$values["customer_draft_contract_confirm"]->caption,"label0"=>"خیر","label1"=>"بله","value"=>$values["customer_draft_contract_confirm"]->integer_value])
        @include("component.input._radio_box01",["id"=>$values["customer_draft_contract_required_init_confirm"]->key,"label"=>$values["customer_draft_contract_required_init_confirm"]->caption,"label0"=>"خیر","label1"=>"بله","value"=>$values["customer_draft_contract_required_init_confirm"]->integer_value])
        @include("component.input._select",[
                   "id"=>$values["customer_draft_contract_post_ids_for_init_confirm"]->key,
                   "label"=>$values["customer_draft_contract_post_ids_for_init_confirm"]->caption,
                   "option"=>$post_option_customer_draft_contract_init_confirm["items"],
                    "class_col"=>"col-md-6"
                 ])
        @include("component.input._radio_box01",["id"=>$values["customer_draft_contract_required_final_confirm"]->key,"label"=>$values["customer_draft_contract_required_final_confirm"]->caption,"label0"=>"خیر","label1"=>"بله","value"=>$values["customer_draft_contract_required_final_confirm"]->integer_value])
        @include("component.input._select",[
               "id"=>$values["customer_draft_contract_post_ids_for_final_confirm"]->key,
               "label"=>$values["customer_draft_contract_post_ids_for_final_confirm"]->caption,
               "option"=>$post_option_customer_draft_contract_final_confirm["items"],
                 "class_col"=>"col-md-6"
               ])
        @include("component.input._radio_box01",["id"=>$values["get_the_customer_image"]->key,"label"=>$values["get_the_customer_image"]->caption,"label0"=>"خیر","label1"=>"بله","value"=>$values["get_the_customer_image"]->integer_value])


        <br/>
        @include("component.input._number",["id"=>$values["max_day_for_special_license_15"]->key,"lable"=>$values["max_day_for_special_license_15"]->caption,"value"=>$values["max_day_for_special_license_15"]->integer_value])
<br/>
        @include("component.input._radio_box01",["id"=>$values["send_order_sms_for_customers"]->key,"label"=>$values["send_order_sms_for_customers"]->caption,"label0"=>"خیر","label1"=>"بله","value"=>$values["send_order_sms_for_customers"]->integer_value])
        @include("component.input._radio_box01",["id"=>$values["send_exit_form_sms_for_customers"]->key,"label"=>$values["send_exit_form_sms_for_customers"]->caption,"label0"=>"خیر","label1"=>"بله","value"=>$values["send_exit_form_sms_for_customers"]->integer_value])
        @include("component.input._radio_box01",["id"=>$values["send_register_sms_for_customers"]->key,"label"=>$values["send_register_sms_for_customers"]->caption,"label0"=>"خیر","label1"=>"بله","value"=>$values["send_register_sms_for_customers"]->integer_value])

    </div>


    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره</button>

</form>
