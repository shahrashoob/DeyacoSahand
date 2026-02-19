<form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="row">

        <div class="col-md-12">

            @include("component.input._radio_box01",["id"=>$values["delivery_to_warehouse_without_register_lot_number_is_allowed"]->key,"label"=>$values["delivery_to_warehouse_without_register_lot_number_is_allowed"]->caption,"value"=>$values["delivery_to_warehouse_without_register_lot_number_is_allowed"]->integer_value,"label0"=>"غیرمجاز","label1"=>"مجاز"])
            @include("component.input._radio_box01",["id"=>$values["checking_product_change_in_entry"]->key,"label"=>$values["checking_product_change_in_entry"]->caption,"value"=>$values["checking_product_change_in_entry"]->integer_value])

            @include("component.input._select",["id"=>$values["send_sms_in_quick_change_packing_post_id"]->key,"lable"=>$values["send_sms_in_quick_change_packing_post_id"]->caption,"option"=>$send_sms_in_quick_change_packing_post_option["items"],"class_col"=>"col-md-6"])

            <div class="w-100"><br/></div>
            @include(
                           "component.input._number",["id"=>$values["delivery_to_warehouse_without_register_lot_number_text"]->key,
                           "lable"=>$values["delivery_to_warehouse_without_register_lot_number_text"]->caption,
                           "value"=>$values["delivery_to_warehouse_without_register_lot_number_text"]->string_value
                       ])
        </div>


    </div>
    <br/>
    <br/>

    <div class="row">

        <div class="col-md-12 center alert alert-info">
           تنظیمات  ثبت تراکنش انبار
        </div>
        @include("component.input._select",["id"=>$values["customer_exit_form_status_id"]->key,"lable"=>$values["customer_exit_form_status_id"]->caption,"option"=>$customer_exit_form_option_status["items"]])
        @include("component.input._select",["id"=>$values["contractor_exit_form_status_id"]->key,"lable"=>$values["contractor_exit_form_status_id"]->caption,"option"=>$contractor_exit_form_option_status["items"]])
        @include("component.input._select",["id"=>$values["machine_exit_form_status_id"]->key,"lable"=>$values["machine_exit_form_status_id"]->caption,"option"=>$machine_exit_form_option_status["items"]])
        @include("component.input._select",["id"=>$values["warehouse_exit_form_status_id"]->key,"lable"=>$values["warehouse_exit_form_status_id"]->caption,"option"=>$warehouse_exit_form_option_status["items"]])
        @include("component.input._select",["id"=>$values["supplier_exit_form_status_id"]->key,"lable"=>$values["supplier_exit_form_status_id"]->caption,"option"=>$supplier_exit_form_option_status["items"]])
        @include("component.input._select",["id"=>$values["worker_exit_form_status_id"]->key,"lable"=>$values["worker_exit_form_status_id"]->caption,"option"=>$worker_exit_form_option_status["items"]])

    </div>

<div class="w-100"><br/></div>
    <div class="row">

        <div class="col-md-12 center alert alert-info">
             تنظیمات بارگیری
        </div>
        @include("component.input._select",["id"=>$values["send_loading_sms_for_customer_in_exit_form_status_id"]->key,"lable"=>$values["send_loading_sms_for_customer_in_exit_form_status_id"]->caption,"option"=>$send_sms_customer_exit_form_option_status["items"],"class_col"=>"col-md-6"])
        @include("component.input._select",["id"=>$values["loading_post_id_sms_for_customer_exit_form_status_id"]->key,"lable"=>$values["loading_post_id_sms_for_customer_exit_form_status_id"]->caption,"option"=>$sms_to_post_id_customer_exit_form_option_status["items"],"class_col"=>"col-md-6"])

        @include("component.input._select",["id"=>$values["send_loading_sms_for_contractor_in_exit_form_status_id"]->key,"lable"=>$values["send_loading_sms_for_contractor_in_exit_form_status_id"]->caption,"option"=>$send_sms_contractor_exit_form_option_status["items"],"class_col"=>"col-md-6"])
        @include("component.input._select",["id"=>$values["loading_post_id_sms_for_contractor_exit_form_status_id"]->key,"lable"=>$values["loading_post_id_sms_for_contractor_exit_form_status_id"]->caption,"option"=>$sms_to_post_id_contractor_exit_form_option_status["items"],"class_col"=>"col-md-6"])

        @include("component.input._select",["id"=>$values["send_loading_sms_for_machine_in_exit_form_status_id"]->key,"lable"=>$values["send_loading_sms_for_machine_in_exit_form_status_id"]->caption,"option"=>$send_sms_machine_exit_form_option_status["items"],"class_col"=>"col-md-6"])
        @include("component.input._select",["id"=>$values["loading_post_id_sms_for_machine_exit_form_status_id"]->key,"lable"=>$values["loading_post_id_sms_for_machine_exit_form_status_id"]->caption,"option"=>$sms_to_post_id_machine_exit_form_option_status["items"],"class_col"=>"col-md-6"])

        @include("component.input._select",["id"=>$values["send_loading_sms_for_warehouse_in_exit_form_status_id"]->key,"lable"=>$values["send_loading_sms_for_warehouse_in_exit_form_status_id"]->caption,"option"=>$send_sms_warehouse_exit_form_option_status["items"],"class_col"=>"col-md-6"])
        @include("component.input._select",["id"=>$values["loading_post_id_sms_for_warehouse_exit_form_status_id"]->key,"lable"=>$values["loading_post_id_sms_for_warehouse_exit_form_status_id"]->caption,"option"=>$sms_to_post_id_warehouse_exit_form_option_status["items"],"class_col"=>"col-md-6"])

        @include("component.input._select",["id"=>$values["send_loading_sms_for_supplier_in_exit_form_status_id"]->key,"lable"=>$values["send_loading_sms_for_supplier_in_exit_form_status_id"]->caption,"option"=>$send_sms_supplier_exit_form_option_status["items"],"class_col"=>"col-md-6"])
        @include("component.input._select",["id"=>$values["loading_post_id_sms_for_supplier_exit_form_status_id"]->key,"lable"=>$values["loading_post_id_sms_for_supplier_exit_form_status_id"]->caption,"option"=>$sms_to_post_id_supplier_exit_form_option_status["items"],"class_col"=>"col-md-6"])

    </div>

<br/>
    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

</form>
