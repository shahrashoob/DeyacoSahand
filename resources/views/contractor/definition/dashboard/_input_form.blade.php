<div class="table-responsive">
    <table class="table table-styling">


        <tr>
            <td>1</td>
            <td>

                <input type="checkbox" id="checking_form_not_delivered_at_register_production"
                       name="checking_form_not_delivered_at_register_production"
                        {{$data_contractor_default_setting->checking_form_not_delivered_at_register_production->value?"checked":""}}
                        {{$data_contractor_default_setting->checking_form_not_delivered_at_register_production->enable ? 'disabled' : ''}}
                >

                آیا وجود فرم ورود به انبار تحویل نشده در زمان ثبت تولید توسط این پیمانکار چک شود؟


            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>2</td>
            <td>

                <input type="checkbox" id="get_packing_form_details"
                       name="get_packing_form_details"
                        {{$data_contractor_default_setting->get_packing_form_details->value?"checked":""}}
                        {{$data_contractor_default_setting->get_packing_form_details->enable ? 'disabled' : ''}}
                >

                آیا جزئیات اطلاعات بسته بندی ها در زمان ثبت تولید از پیمانکار دریافت گردد؟

            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>3</td>
            <td>

                <input type="checkbox" id="input_form_loading_require"
                       name="input_form_loading_require"
                        {{$data_contractor_default_setting->input_form_loading_require->value?"checked":""}}
                        {{$data_contractor_default_setting->input_form_loading_require->enable ? 'disabled' : ''}}
                >
                آیا بعد از ثبت تامین نیاز به ارسال (بارگیری) دارد؟


            </td>
            <td>

            </td>
        </tr>

        <tr>
            <td>4</td>
            <td>

                <input type="checkbox" id="input_form_guarding_require_permission"
                       name="input_form_guarding_require_permission"
                        {{$data_contractor_default_setting->input_form_guarding_require_permission->value?"checked":""}}
                        {{$data_contractor_default_setting->input_form_guarding_require_permission->enable ? 'disabled' : ''}}
                >
                آیا فرم ورود نیاز به تایید نگهبانی دارد؟


            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>5</td>
            <td>

                <input type="checkbox" id="input_form_quality_control_permission"
                       name="input_form_quality_control_permission"
                        {{$data_contractor_default_setting->input_form_quality_control_permission->value?"checked":""}}
                        {{$data_contractor_default_setting->input_form_quality_control_permission->enable ? 'disabled' : ''}}
                >
                آیا فرم ورود نیاز به تایید کنترل کیفیت دارد؟


            </td>
            <td>

            </td>
        </tr>
    </table>
</div>



