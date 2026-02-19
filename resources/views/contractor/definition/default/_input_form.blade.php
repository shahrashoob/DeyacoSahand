<div class="table-responsive">
    <table class="table table-styling">

        <tr>
            <th>ردیف</th>
            <th>استفاده از تنظیمات پیش فرض</th>
            <th>توضیحات</th>
        </tr>

        <tr>
            <td>1</td>
            <td>
                <input type="checkbox" name="checking_form_not_delivered_at_register_production[enable]" {{($data_contractor_default_setting->checking_form_not_delivered_at_register_production->enable ?? 0)?"checked":""}}>
            </td>
            <td>
                <input type="checkbox" name="checking_form_not_delivered_at_register_production[value]" {{($data_contractor_default_setting->checking_form_not_delivered_at_register_production->value ?? 0)?"checked":""}}>
                آیا وجود فرم ورود به انبار تحویل نشده در زمان ثبت تولید توسط این پیمانکار چک شود؟
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>2</td>
            <td>
                <input type="checkbox" name="get_packing_form_details[enable]" {{($data_contractor_default_setting->get_packing_form_details->enable ?? 0)?"checked":""}}>
            </td>
            <td>
                <input type="checkbox" name="get_packing_form_details[value]" {{($data_contractor_default_setting->get_packing_form_details->value ?? 0)?"checked":""}}>
                آیا جزئیات اطلاعات بسته بندی ها در زمان ثبت تولید از پیمانکار دریافت گردد؟
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>3</td>
            <td>
                <input type="checkbox" name="input_form_loading_require[enable]" {{($data_contractor_default_setting->input_form_loading_require->enable ?? 0)?"checked":""}}>
            </td>
            <td>
                <input type="checkbox" name="input_form_loading_require[value]" {{($data_contractor_default_setting->input_form_loading_require->value ?? 0)?"checked":""}}>
                آیا بعد از ثبت تامین نیاز به ارسال (بارگیری) دارد؟

            </td>
            <td></td>
        </tr>

        <tr>
            <td>4</td>
            <td>
                <input type="checkbox" name="input_form_guarding_require_permission[enable]" {{($data_contractor_default_setting->input_form_guarding_require_permission->enable?? 0)?"checked":""}}>
            </td>
            <td>

                <input type="checkbox" name="input_form_guarding_require_permission[value]" {{($data_contractor_default_setting->input_form_guarding_require_permission->value?? 0)?"checked":""}}>
                آیا فرم ورود نیاز به تایید نگهبانی دارد؟
            </td>
            <td>

            </td>
        </tr>

        <tr>
            <td>5</td>
            <td><input type="checkbox" name="input_form_quality_control_permission[enable]" {{($data_contractor_default_setting->input_form_quality_control_permission->enable?? 0)?"checked":""}}>
            </td>
            <td>

                <input type="checkbox" name="input_form_quality_control_permission[value]" {{($data_contractor_default_setting->input_form_quality_control_permission->value?? 0)?"checked":""}}>
                آیا فرم ورود نیاز به تایید کنترل کیفیت دارد؟
            </td>
            <td></td>
        </tr>
    </table>
</div>



