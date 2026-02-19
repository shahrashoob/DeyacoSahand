
@if(!$data_contractor_default_setting->checking_form_not_delivered_at_register_production->enable)

    @include("component.input._radio_box01",[
                  "id"=>"checking_form_not_delivered_at_register_production",
                  "label"=>"آیا وجود فرم ورود به انبار تحویل نشده در زمان ثبت تولید توسط این پیمانکار چک شود؟",
                  "label0"=>"خیر","label1"=>"بله",
                  "value"=>$data_contractor_default_setting->checking_form_not_delivered_at_register_production->value])

@endif
@if(!$data_contractor_default_setting->get_packing_form_details->enable)

    @include("component.input._radio_box01",[
                  "id"=>"get_packing_form_details",
                  "label"=>"  آیا جزئیات اطلاعات بسته بندی ها در زمان ثبت تولید از پیمانکار دریافت گردد؟",
                  "label0"=>"خیر","label1"=>"بله",
                  "value"=>$data_contractor_default_setting->get_packing_form_details->value])

@endif
    @if(!$data_contractor_default_setting->input_form_loading_require->enable)

        @include("component.input._radio_box01",[
                      "id"=>"input_form_loading_require",
                      "label"=>"  آیا بعد از ثبت تامین نیاز به ارسال (بارگیری) دارد؟",
                      "label0"=>"خیر","label1"=>"بله",
                      "value"=>$data_contractor_default_setting->input_form_loading_require->value])

    @endif
    @if(!$data_contractor_default_setting->input_form_guarding_require_permission->enable)

        @include("component.input._radio_box01",[
                      "id"=>"input_form_guarding_require_permission",
                      "label"=>" آیا فرم ورود نیاز به تایید نگهبانی دارد؟",
                      "label0"=>"خیر","label1"=>"بله",
                      "value"=>$data_contractor_default_setting->input_form_guarding_require_permission->value])

    @endif
    @if(!$data_contractor_default_setting->input_form_quality_control_permission->enable)

        @include("component.input._radio_box01",[
                                "id"=>"input_form_quality_control_permission",
                                "label"=>"    آیا فرم ورود نیاز به تایید کنترل کیفیت دارد؟",
                                "label0"=>"خیر","label1"=>"بله",
                                "value"=>$data_contractor_default_setting->input_form_quality_control_permission->value])

    @endif



