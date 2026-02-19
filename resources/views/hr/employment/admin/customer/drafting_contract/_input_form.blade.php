<div class="table-responsive">
    <table class="table table-styling">

        @if(isset($data_customer_default_setting['input_form']['input_form_guarding_require_permission']['enable']) &&
                   ! $data_customer_default_setting['input_form']['input_form_guarding_require_permission']['enable']  )
            <tr>
                <td>
                    @include("component.input._radio_box01",[
                                            "id"=>"input_form_guarding_require_permission",
                                            "label"=>"  آیا فرم ورود به انبار نیاز به تایید نگهبانی دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>  $data_customer_default_setting['input_form']['input_form_guarding_require_permission']['value']])

                </td>

            </tr>
        @endif

        @if(isset($data_customer_default_setting['input_form']['input_form_loading_require']['enable']) &&
           ! $data_customer_default_setting['input_form']['input_form_loading_require']['enable']  )
            <tr>
                <td>
                    @include("component.input._radio_box01",[
                                            "id"=>"input_form_loading_require",
                                            "label"=>"آیا فرم ورود نیاز به ارسال (بارگیری) دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>  $data_customer_default_setting['input_form']['input_form_loading_require']['value']])

                </td>

            </tr>
        @endif
        @if(isset($data_customer_default_setting['input_form']['input_form_quality_control_permission']['enable']) &&
                ! $data_customer_default_setting['input_form']['input_form_quality_control_permission']['enable']  )
            <tr>

                <td>
                    @include("component.input._radio_box01",[
                                            "id"=>"input_form_quality_control_permission",
                                            "label"=>"    آیا فرم ورود نیاز به تایید کنترل کیفیت دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>  $data_customer_default_setting['input_form']['input_form_quality_control_permission']['value']])


                </td>

            </tr>
        @endif

    </table>
</div>
