
<div class="table-responsive">
    <table class="table table-styling">
        <tr>
            <th>ردیف</th>
            <th>توضیحات</th>
       
        </tr>
        <tr>
            <td>1</td>
            <td>

                <input type="checkbox"
                       name="input_form_guarding_require_permission"
                        {{ isset($data_customer_default_setting['input_form']['input_form_guarding_require_permission']['value']) &&
                            $data_customer_default_setting['input_form']['input_form_guarding_require_permission']['value'] ? "checked='checked'" : "" }}
                        {{ isset($data_customer_default_setting['input_form']['input_form_guarding_require_permission']['enable']) &&
                            $data_customer_default_setting['input_form']['input_form_guarding_require_permission']['enable'] ? "disabled" : "" }}
                >
                ایا فرم ورود به انبار نیاز به تایید نگهبانی دارد؟


            </td>
   
        </tr>

        <tr>
            <td>2</td>
            <td>

                <input type="checkbox"
                       name="input_form_loading_require"
                        {{ isset($data_customer_default_setting['input_form']['input_form_loading_require']['value']) &&
                                 $data_customer_default_setting['input_form']['input_form_loading_require']['value'] ? "checked='checked'" : "" }}
                        {{ isset($data_customer_default_setting['input_form']['input_form_loading_require']['enable']) &&
                                 $data_customer_default_setting['input_form']['input_form_loading_require']['enable'] ? "disabled" : "" }}

                >
                آیا فرم ورود نیاز به ارسال (بارگیری) دارد؟


            </td>

        </tr>
        <tr>
            <td>3</td>
            <td>

                <input type="checkbox" id="input_form_quality_control_permission"
                       name="input_form_quality_control_permission"
                        {{ isset($data_customer_default_setting['input_form']['input_form_quality_control_permission']['value']) &&
                                       $data_customer_default_setting['input_form']['input_form_quality_control_permission']['value'] ? "checked='checked'" : "" }}
                        {{isset($data_customer_default_setting['input_form']['input_form_quality_control_permission']['enable']) &&
                                         $data_customer_default_setting['input_form']['input_form_quality_control_permission']['enable'] ? "disabled" : "" }}
                >
                آیا فرم ورود نیاز به تایید کنترل کیفیت دارد؟


            </td>
            <td>

            </td>
        </tr>
     
    </table>
</div>
