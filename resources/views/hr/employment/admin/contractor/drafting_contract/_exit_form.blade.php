
<div class="table-responsive">
    <table class="table table-styling">

        <tr>
            <th></th>
            <th>
                پست جهت اطلاع رسانی
            </th>
        </tr>
        @if(!$data_contractor_default_setting->exit_form_require_draft_permission->enable)
            <tr>
                <td>


                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_require_draft_permission",
                                            "label"=>"   آیا برگ خروج از انبار نیاز به تایید پیش نویس (واحد مالی) دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>$data_contractor_default_setting->exit_form_require_draft_permission->value])
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
        @endif
        @if(!$data_contractor_default_setting->exit_form_require_permission->enable)
            <tr>
                <td>

                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_require_permission",
                                            "label"=>" آیا برگ خروج از انبار نیاز به تایید نهایی (واحد مالی) دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>$data_contractor_default_setting->exit_form_require_permission->value])


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
        @endif
        @if(!$data_contractor_default_setting->exit_form_loading_require_permission->enable)
            <tr>
                <td>


                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_loading_require_permission",
                                            "label"=>"آیا برگ خروج از انبار نیاز به ارسال دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>$data_contractor_default_setting->exit_form_loading_require_permission->value])
                </td>
                <td>

                </td>
            </tr>
        @endif
        @if(!$data_contractor_default_setting->exit_form_guarding_require_permission->enable)
            <tr>
                <td>


                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_guarding_require_permission",
                                            "label"=>"آیا برگ خروج از انبار نیاز به تایید نگهبانی دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>$data_contractor_default_setting->exit_form_guarding_require_permission->value])
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
        @endif
        @if(!$data_contractor_default_setting->checking_carrier_at_delivery_of_product->enable)
                <tr>

                    <td>

                        @include("component.input._radio_box01",[
                                                         "id"=>"checking_carrier_at_delivery_of_product",
                                                         "label"=>"        آیا کد بسته بندی / حامل در زمان تایید تحویل کالا توسط پیمانکار چک شود؟",
                                                         "label0"=>"خیر","label1"=>"بله",
                                                         "value"=>$data_contractor_default_setting->checking_carrier_at_delivery_of_product->value])

                    </td>
                    <td>

                    </td>
                </tr>
        @endif
    </table>
</div>
