
    @if(!$data_supplier_default_setting->get_packing_form_details->enable)

        @include("component.input._radio_box01",[
                "id"=>"get_packing_form_details",
                "label"=>"آیا جزئیات اطلاعات بسته بندی ها در زمان ثبت تامین از تامین کننده دریافت گردد؟",
                "label0"=>"خیر","label1"=>"بله",
                "value"=>$data_supplier_default_setting->get_packing_form_details->value])

    @endif
    @if(!$data_supplier_default_setting->input_form_loading_require->enable)

        @include("component.input._radio_box01",[
                      "id"=>"input_form_loading_require",
                      "label"=>"  آیا بعد از ثبت تامین نیاز به ارسال (بارگیری) دارد؟",
                      "label0"=>"خیر","label1"=>"بله",
                      "value"=>$data_supplier_default_setting->input_form_loading_require->value])

    @endif
    @if(!$data_supplier_default_setting->input_form_guarding_require_permission->enable)

        @include("component.input._radio_box01",[
                      "id"=>"input_form_guarding_require_permission",
                      "label"=>" آیا فرم ورود نیاز به تایید نگهبانی دارد؟",
                      "label0"=>"خیر","label1"=>"بله",
                      "value"=>$data_supplier_default_setting->input_form_guarding_require_permission->value])

    @endif
    @if(!$data_supplier_default_setting->input_form_quality_control_permission->enable)

        @include("component.input._radio_box01",[
                                "id"=>"input_form_quality_control_permission",
                                "label"=>"    آیا فرم ورود نیاز به تایید کنترل کیفیت دارد؟",
                                "label0"=>"خیر","label1"=>"بله",
                                "value"=>$data_supplier_default_setting->input_form_quality_control_permission->value])

    @endif



