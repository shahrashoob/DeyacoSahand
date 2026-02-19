<div class="table-responsive">
    <table class="table table-styling">
        <tr>
            <th></th>
            <th>
                پست جهت اطلاع رسانی
            </th>
        </tr>

        <tr>
            <td>1</td>
            <td>

                <input type="checkbox" id="exit_form_require_draft_permission"
                       name="exit_form_require_draft_permission"
                        {{$data_contractor_default_setting->exit_form_require_draft_permission->value?"checked":""}}
                        {{$data_contractor_default_setting->exit_form_require_draft_permission->enable ? 'disabled' : ''}}
                >
                آیا برگ خروج از انبار نیاز به تایید پیش نویس (واحد مالی) دارد؟


            </td>
            <td>
                <div class="col-md-12">
                    @include("component.input._select_simple",[
                                        "id"=>"exit_form_require_draft_permission_post_id",
                                        "option"=>$post_option_list["exit_form_require_draft_permission_post_id"]["items"],
                                        "val"=>$post_option_list["exit_form_require_draft_permission_post_id"]["value"],
                                        "text"=>$post_option_list["exit_form_require_draft_permission_post_id"]["text"],
                                        "class_col"=>"",
                                        "readonly"=>$data_contractor_default_setting->exit_form_require_draft_permission->enable ? 'readonly' : ''
                                        ])
                </div>
            </td>
        </tr>

        <tr>
            <td>2</td>
            <td>

                <input type="checkbox" id="exit_form_require_permission"
                       name="exit_form_require_permission"
                        {{$data_contractor_default_setting->exit_form_require_permission->value?"checked":""}}
                        {{$data_contractor_default_setting->exit_form_require_permission->enable ? 'disabled' : ''}}
                >
                آیا برگ خروج از انبار نیاز به تایید نهایی (واحد مالی) دارد؟


            </td>
            <td>
                <div class="col-md-12">
                    @include("component.input._select_simple",[
                                        "id"=>"exit_form_require_permission_post_id",
                                        "option"=>$post_option_list["exit_form_require_permission_post_id"]["items"],
                                        "val"=>$post_option_list["exit_form_require_permission_post_id"]["value"],
                                        "text"=>$post_option_list["exit_form_require_permission_post_id"]["text"],
                                        "class_col"=>"",
                                          "readonly"=>$data_contractor_default_setting->exit_form_require_permission->enable ? 'readonly' : ''
                                        ])
                </div>
            </td>
        </tr>
        <tr>
            <td>3</td>
            <td>

                <input type="checkbox" id="exit_form_loading_require_permission"
                       name="exit_form_loading_require_permission"
                        {{$data_contractor_default_setting->exit_form_loading_require_permission->value?"checked":""}}
                        {{$data_contractor_default_setting->exit_form_loading_require_permission->enable ? 'disabled' : ''}}
                >
                آیا برگ خروج از انبار نیاز به ارسال دارد؟


            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>4</td>
            <td>

                <input type="checkbox" id="exit_form_guarding_require_permission"
                       name="exit_form_guarding_require_permission"
                        {{$data_contractor_default_setting->exit_form_guarding_require_permission->value?"checked":""}}
                        {{$data_contractor_default_setting->exit_form_guarding_require_permission->enable ? 'disabled' : ''}}
                >
                آیا برگ خروج از انبار نیاز به تایید نگهبانی دارد؟


            </td>
            <td>
                <div class="col-md-12">
                    @include("component.input._select_simple",[
                                        "id"=>"exit_form_guarding_require_permission_post_id",
                                        "option"=>$post_option_list["exit_form_guarding_require_permission_post_id"]["items"],
                                        "val"=>$post_option_list["exit_form_guarding_require_permission_post_id"]["value"],
                                        "text"=>$post_option_list["exit_form_guarding_require_permission_post_id"]["text"],
                                        "class_col"=>"",
                                          "readonly"=>$data_contractor_default_setting->exit_form_require_permission->enable ? 'readonly' : ''
                                        ])
                </div>
            </td>
        </tr>
                <tr>
                    <td>5</td>
                    <td>

                        <input type="checkbox" id="checking_carrier_at_delivery_of_product"
                               name="checking_carrier_at_delivery_of_product" {{$data_contractor_default_setting->checking_carrier_at_delivery_of_product->value?"checked":""}}
                                {{$data_contractor_default_setting->checking_carrier_at_delivery_of_product->enable ? 'disabled' : ''}}
                        >
                        آیا کد بسته بندی / حامل در زمان تایید تحویل کالا توسط پیمانکار چک شود؟


                    </td>
                    <td>

                    </td>
                </tr>
    </table>
</div>
