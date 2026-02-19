<div class="table-responsive">
    <table class="table table-styling">
        <tr>
            <th>توضیحات</th>

        </tr>
        @if($employment->customer->input_form_guarding_require_permission)
            <tr>

                <td>  فرم ورود به انبار نیاز به تایید نگهبانی دارد. </td>

            </tr>
        @endif
        @if($employment->customer->input_form_loading_require)
            <tr>

                <td> فرم ورود نیاز به ارسال (بارگیری) دارد.</td>

            </tr>
        @endif

        @if($employment->customer->input_form_quality_control_permission)
            <tr>

                <td> فرم ورود نیاز به تایید کنترل کیفیت دارد.</td>

            </tr>
        @endif
    </table>
</div>
