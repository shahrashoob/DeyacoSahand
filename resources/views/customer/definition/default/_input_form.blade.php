<div class="table-responsive">
    <table class="table table-styling">
        <tr>
            <th></th>
            <th>استفاده از تنظیمات پیش فرض</th>
            <th>
                توضیحات
            </th>
        </tr>

        <tr>
            <td>1</td>
            <td>
                <input type="checkbox"
                       name="input_form_guarding_require_permission[enable]" {{ isset($data_customer_default_setting['input_form']['input_form_guarding_require_permission']['enable']) &&
 $data_customer_default_setting['input_form']['input_form_guarding_require_permission']['enable'] ? "checked='checked'" : "" }}
                >
            </td>
            <td>
                <input type="checkbox"
                       name="input_form_guarding_require_permission[value]" {{ isset($data_customer_default_setting['input_form']['input_form_guarding_require_permission']['value']) &&
 $data_customer_default_setting['input_form']['input_form_guarding_require_permission']['value'] ? "checked='checked'" : "" }}

                >
                ایا فرم ورود به انبار نیاز به تایید نگهبانی دارد؟


            </td>

        </tr>

        <tr>
            <td>2</td>
            <td>

                <input type="checkbox"
                       name="input_form_loading_require[enable]"{{ isset($data_customer_default_setting['input_form']['input_form_loading_require']['enable']) &&
                                        $data_customer_default_setting['input_form']['input_form_loading_require']['enable'] ? "checked='checked'" : "" }}

                >
            </td>
            <td>

                <input type="checkbox"
                       name="input_form_loading_require[value]"{{ isset($data_customer_default_setting['input_form']['input_form_loading_require']['value']) &&
                                         $data_customer_default_setting['input_form']['input_form_loading_require']['value'] ? "checked='checked'" : "" }}

                >
                آیا فرم ورود نیاز به ارسال (بارگیری) دارد؟


            </td>

        </tr>
        <tr>
            <td>2</td>
            <td>

                <input type="checkbox"
                       name="input_form_quality_control_permission[enable]"{{ isset($data_customer_default_setting['input_form']['input_form_quality_control_permission']['enable']) &&
                                        $data_customer_default_setting['input_form']['input_form_quality_control_permission']['enable'] ? "checked='checked'" : "" }}

                >
            </td>
            <td>

                <input type="checkbox"
                       name="input_form_quality_control_permission[value]"{{ isset($data_customer_default_setting['input_form']['input_form_quality_control_permission']['value']) &&
                                         $data_customer_default_setting['input_form']['input_form_quality_control_permission']['value'] ? "checked='checked'" : "" }}

                >
                آیا فرم های ورود نیاز به تایید کنترل کیفیت دارد


            </td>

        </tr>


    </table>
</div>
