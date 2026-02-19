<div class="table-responsive">
    <table class="table table-styling">
        <tr>
            <th>تاییدیه های مورد نیاز</th>
            <th>پست جهت اطلاع رسانی</th>
        </tr>
        @if($employment->supplier->exit_form_require_quality_permission)
            <tr>

                <td>برگ خروج از انبار نیاز به تایید کنترل کیفیت دارد</td>
                <td></td>
            </tr>
        @endif
        @if($employment->supplier->exit_form_require_draft_permission)
            <tr>

                <td>  برگ خروج از انبار نیاز به تایید پیش نویس (واحد مالی) </td>
                <td>{{$employment->supplier->exit_form_require_draft_permission_post->caption ?? ""}}</td>
            </tr>
        @endif
        @if($employment->supplier->exit_form_require_permission)
            <tr>

                <td> برگ خروج از انبار نیاز به تایید نهایی (واحد مالی) دارد.</td>
                <td>{{$employment->supplier->exit_form_require_permission_post->caption ?? ""}}</td>
            </tr>
        @endif

        @if($employment->supplier->exit_form_guarding_require_permission)
            <tr>

                <td> برگ خروج از انبار نیاز به تایید نگهبانی دارد.</td>
                <td>{{$employment->supplier->exit_form_guarding_require_permission_post->caption ?? ""}}</td>
            </tr>
        @endif
        @if($employment->supplier->exit_form_loading_require_permission)
            <tr>

                <td>  برگ خروج از انبار نیاز به ارسال دارد.</td>
                <td></td>
            </tr>
        @endif

        @if($employment->supplier->checking_carrier_at_delivery_of_product)
            <tr>

                <td> کد بسته بندی / حامل در زمان تایید تحویل کالا توسط مشتری  باید چک شود</td>
                <td></td>
            </tr>
        @endif
    </table>
</div>
