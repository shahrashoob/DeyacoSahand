<div class="table-responsive">
    <table class="table table-styling">
        <tr>
            <th>تاییدیه های مورد نیاز</th>
        </tr>
        @if($employment->contractor->checking_form_not_delivered_at_register_production)
            <tr>

                <td>فرم ورود به انبار تحویل نشده در زمان ثبت تولید توسط این پیمانکار باید چک شود.  </td>
            </tr>
        @endif
        @if($employment->contractor->get_packing_form_details)
            <tr>

                <td>جزئیات اطلاعات بسته بندی ها در زمان ثبت تولید از پیمانکار  باید دریافت گردد.</td>
            </tr>
        @endif
        @if($employment->contractor->input_form_loading_require)
            <tr>

                <td> بعد از ثبت تامین نیاز به ارسال (بارگیری) دارد.</td>
            </tr>
        @endif

        @if($employment->contractor->input_form_guarding_require_permission)
            <tr>

                <td> فرم ورود نیاز به تایید نگهبانی دارد.</td>

            </tr>
        @endif
        @if($employment->contractor->input_form_quality_control_permission)
            <tr>

                <td> فرم ورود نیاز به تایید کنترل کیفیت دارد.</td>
                <td></td>
            </tr>
        @endif

    </table>
</div>
