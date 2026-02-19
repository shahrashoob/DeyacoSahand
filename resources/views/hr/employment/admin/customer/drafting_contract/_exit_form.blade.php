@include("component.input._number",
["id"=>"the_max_day_allowed_to_conform_exit_form_to",
"label"=>"حداکثر زمان (روز) مجاز تایید برگ خروج از انبار توسط مشتری",
"value"=>$data_customer_default_setting['exit_form']['the_max_day_allowed_to_conform_exit_form_to']??1,
"class_col"=>"col-md-12",
])

@include("component.input._number",
["id"=>"the_max_day_for_reject_product",
"label"=>"حداکثر زمان (روز) مجاز جهت برگشت کالا توسط مشتری (بعد از تایید دریافت)",
"value"=>$data_customer_default_setting['exit_form']['the_max_day_for_reject_product']??1,
"class_col"=>"col-md-12",
])

<div class="table-responsive">
    <table class="table table-styling">
        <tr>
            <th></th>
            <th>
                پست جهت اطلاع رسانی
            </th>
        </tr>
        @if(isset($data_customer_default_setting['exit_form']['exit_form_require_draft_permission']['enable']) &&
                   ! $data_customer_default_setting['exit_form']['exit_form_require_draft_permission']['enable']  )
            <tr>
                <td>
                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_require_draft_permission",
                                            "label"=>"  آیا برگ خروج از انبار نیاز به تایید پیش نویس (واحد مالی) دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>  $data_customer_default_setting['exit_form']['exit_form_require_draft_permission']['value']])

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
        @endif

        @if(isset($data_customer_default_setting['exit_form']['exit_form_require_permission']['enable']) &&
           ! $data_customer_default_setting['exit_form']['exit_form_require_permission']['enable']  )
            <tr>
                <td>
                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_require_permission",
                                            "label"=>" آیا برگ خروج از انبار نیاز به تایید نهایی (واحد مالی) دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>  $data_customer_default_setting['exit_form']['exit_form_require_permission']['value']])

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
        @endif
        @if(isset($data_customer_default_setting['exit_form']['exit_form_loading_require_permission']['enable']) &&
                ! $data_customer_default_setting['exit_form']['exit_form_loading_require_permission']['enable']  )
            <tr>

                <td>
                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_loading_require_permission",
                                            "label"=>"آیا برگ خروج از انبار نیاز به ارسال دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>  $data_customer_default_setting['exit_form']['exit_form_loading_require_permission']['value']])


                </td>
                <td></td>
            </tr>
        @endif
        @if(isset($data_customer_default_setting['exit_form']['exit_form_guarding_require_permission']['enable']) &&
   ! $data_customer_default_setting['exit_form']['exit_form_guarding_require_permission']['enable']  )
            <tr>
                <td>
                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_guarding_require_permission",
                                            "label"=>" آیا برگ خروج از انبار نیاز به تایید نگهبانی دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>  $data_customer_default_setting['exit_form']['exit_form_guarding_require_permission']['value']])

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
        @endif
        @if(isset($data_customer_default_setting['exit_form']['checking_carrier_at_delivery_of_product']['enable']) &&
        ! $data_customer_default_setting['exit_form']['checking_carrier_at_delivery_of_product']['enable']  )
            <tr>

                <td>
                    @include("component.input._radio_box01",[
                                            "id"=>"checking_carrier_at_delivery_of_product",
                                            "label"=>" آیا کد بسته بندی / حامل در زمان تایید تحویل کالا توسط مشتری چک شود؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>  $data_customer_default_setting['exit_form']['checking_carrier_at_delivery_of_product']['value']])


                </td>
                <td></td>
            </tr>
        @endif
        @if(isset($data_customer_default_setting['exit_form']['exit_form_require_demands_permission']['enable']) &&
! $data_customer_default_setting['exit_form']['exit_form_require_demands_permission']['enable']  )
            <tr>

                <td>
                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_require_demands_permission",
                                            "label"=>" آیا پیش نویس برگ خروج (فرم خروج از انبار) نیاز به تایید وصول مطالبات دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>  $data_customer_default_setting['exit_form']['exit_form_require_demands_permission']['value']])


                </td>
                <td>
                    <div class="col-md-12">
                        @include("component.input._select_simple",[
                                        "id"=>"exit_form_require_demands_permission_post_id",
                                        "option"=>$post_option_list["exit_form_require_demands_permission_post_id"]["items"],
                                        "val"=>$post_option_list["exit_form_require_demands_permission_post_id"]["value"],
                                        "text"=>$post_option_list["exit_form_require_demands_permission_post_id"]["text"],
                                        "class_col"=>""
                                        ])
                    </div>
                </td>
            </tr>
        @endif
    </table>
</div>
