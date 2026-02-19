<div class="table-responsive">
    <table class="table table-styling">
        <tr>
            <th>ردیف</th>
            <th>استفاده از تنظیمات پیش فرض</th>

            <th>توضیحات</th>
            <th>
                پست جهت اطلاع رسانی
            </th>
        </tr>


        <tr>
            <td>1</td>
            <td>

                <input type="checkbox"
                       name="exit_form_require_draft_permission[enable]" {{($data_contractor_default_setting->exit_form_require_draft_permission->enable?? 0)?"checked":""}}>
            </td>
            <td>

                <input type="checkbox"
                       name="exit_form_require_draft_permission[value]" {{($data_contractor_default_setting->exit_form_require_draft_permission->value?? 0)?"checked":""}}>
                آیا برگ خروج از انبار نیاز به تایید پیش نویس (واحد مالی) دارد؟
            </td>
            <td>
                <div class="col-md-12">
                    @include("component.input._select_simple",[
                                        "id"=>"exit_form_require_draft_permission_post_id",
                                        "option"=>$post_option_list["exit_form_require_draft_permission_post_id"]["items"],
                                        "val"=>$post_option_list["exit_form_require_draft_permission_post_id"]["value"],
                                        "text"=>$post_option_list["exit_form_require_draft_permission_post_id"]["text"],
                                        "class_col"=>""
                                        ])
                </div>
            </td>
        </tr>

        <tr>
            <td>2</td>
            <td>

                <input type="checkbox"
                       name="exit_form_require_permission[enable]" {{($data_contractor_default_setting->exit_form_require_permission->enable?? 0)?"checked":""}}>
            </td>
            <td>

                <input type="checkbox"
                       name="exit_form_require_permission[value]" {{($data_contractor_default_setting->exit_form_require_permission->value?? 0)?"checked":""}}>
                آیا برگ خروج از انبار نیاز به تایید نهایی (واحد مالی) دارد؟


            </td>
            <td>
                <div class="col-md-12">
                    @include("component.input._select_simple",[
                                        "id"=>"exit_form_require_permission_post_id",
                                        "option"=>$post_option_list["exit_form_require_permission_post_id"]["items"],
                                        "val"=>$post_option_list["exit_form_require_permission_post_id"]["value"],
                                        "text"=>$post_option_list["exit_form_require_permission_post_id"]["text"],
                                        "class_col"=>""
                                        ])
                </div>
            </td>
        </tr>
        <tr>
            <td>3</td>
            <td>
                <input type="checkbox"
                       name="exit_form_loading_require_permission[enable]" {{($data_contractor_default_setting->exit_form_loading_require_permission->enable?? 0)?"checked":""}}>
            </td>
            <td>

                <input type="checkbox"
                       name="exit_form_loading_require_permission[value]" {{($data_contractor_default_setting->exit_form_loading_require_permission->value?? 0)?"checked":""}}>
                آیا برگ خروج از انبار نیاز به ارسال دارد؟


            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>4</td>
            <td>

                <input type="checkbox"
                       name="exit_form_guarding_require_permission[enable]" {{($data_contractor_default_setting->exit_form_guarding_require_permission->enable?? 0)?"checked":""}}>
            </td>
            <td>

                <input type="checkbox"
                       name="exit_form_guarding_require_permission[value]" {{($data_contractor_default_setting->exit_form_guarding_require_permission->value?? 0)?"checked":""}}>
                آیا برگ خروج از انبار نیاز به تایید نگهبانی دارد؟


            </td>
            <td>
                <div class="col-md-12">
                    @include("component.input._select_simple",[
                                        "id"=>"exit_form_guarding_require_permission_post_id",
                                        "option"=>$post_option_list["exit_form_guarding_require_permission_post_id"]["items"],
                                        "val"=>$post_option_list["exit_form_guarding_require_permission_post_id"]["value"],
                                        "text"=>$post_option_list["exit_form_guarding_require_permission_post_id"]["text"],
                                        "class_col"=>""
                                        ])
                </div>
            </td>
        </tr>
        <tr>
            <td>5</td>
            <td>
                <input type="checkbox"
                       name="checking_carrier_at_delivery_of_product[enable]" {{($data_contractor_default_setting->checking_carrier_at_delivery_of_product->enable?? 0)?"checked":""}}>
            </td>
            <td>

                <input type="checkbox"
                       name="checking_carrier_at_delivery_of_product[value]" {{($data_contractor_default_setting->checking_carrier_at_delivery_of_product->value?? 0)?"checked":""}}>
                آیا کد بسته بندی / حامل در زمان تایید تحویل کالا توسط پیمانکار چک شود؟


            </td>
            <td>

            </td>
        </tr>
    </table>
</div>
