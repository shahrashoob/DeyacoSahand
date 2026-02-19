
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
                       name="input_form_guarding_require_permission" {{$customer->input_form_guarding_require_permission?"checked='checked'":""}}
                >
                ایا فرم ورود به انبار نیاز به تایید نگهبانی دارد؟


            </td>

        </tr>

        <tr>
            <td>2</td>
            <td>

                <input type="checkbox"
                       name="input_form_loading_require" {{$customer->input_form_loading_require?"checked='checked'":""}}
                >
                آیا فرم ورود نیاز به ارسال (بارگیری) دارد؟


            </td>

        </tr>
        <tr>
            <td>3</td>
            <td>

                <input type="checkbox" id="input_form_quality_control_permission"
                       name="input_form_quality_control_permission" {{$customer->input_form_quality_control_permission?"checked":""}}
                >
                آیا فرم ورود نیاز به تایید کنترل کیفیت دارد؟


            </td>
            <td>

            </td>
        </tr>

    </table>
</div>
