@include("component.input._number",
["id"=>"the_max_day_allowed_to_conform_exit_form_to",
"label"=>"حداکثر زمان (روز) مجاز تایید برگ خروج از انبار توسط مشتری",
"value"=>$customer->the_max_day_allowed_to_conform_exit_form_to??1,
"class_col"=>"col-md-12",
])

@include("component.input._number",
["id"=>"the_max_day_for_reject_product",
"label"=>"حداکثر زمان (روز) مجاز جهت برگشت کالا توسط مشتری (بعد از تایید دریافت)",
"value"=>$customer->the_max_day_for_reject_product??1,
"class_col"=>"col-md-12",
])



<div class="table-responsive">
    <table class="table table-styling">
        <tr>
            <th></th>
            <th></th>
            <th>
                پست جهت اطلاع رسانی
            </th>
        </tr>

        <tr>
            <td>1</td>
            <td>

                <input type="checkbox"
                       name="exit_form_require_draft_permission" {{$customer->exit_form_require_draft_permission?"checked='checked'":""}}
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
                                        "class_col"=>""
                                        ])
                </div>
            </td>
        </tr>

        <tr>
            <td>2</td>
            <td>

                <input type="checkbox"
                       name="exit_form_require_permission" {{$customer->exit_form_require_permission?"checked='checked'":""}}
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
                                        "class_col"=>""
                                        ])
                </div>
            </td>
        </tr>
        <tr>
            <td>3</td>
            <td>

                <input type="checkbox" id="exit_form_loading_require_permission"
                       name="exit_form_loading_require_permission" {{$customer->exit_form_loading_require_permission?"checked":""}}
                >
                آیا برگ خروج از انبار نیاز به ارسال دارد؟


            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>4</td>
            <td>

                <input type="checkbox"
                       name="exit_form_guarding_require_permission" {{$customer->exit_form_guarding_require_permission?"checked='checked'":""}}
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
                                        "class_col"=>""
                                        ])
                </div>
            </td>
        </tr>

        <tr>
            <td>5</td>
            <td>

                <input type="checkbox" id="checking_carrier_at_delivery_of_product"
                       name="checking_carrier_at_delivery_of_product" {{$customer->checking_carrier_at_delivery_of_product?"checked":""}}
                >
                آیا   کد بسته بندی / حامل در زمان تایید تحویل کالا توسط مشتری چک شود؟


            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>6</td>
            <td>

                <input type="checkbox" id="exit_form_require_demands_permission"
                       name="exit_form_require_demands_permission" {{$customer->exit_form_require_demands_permission?"checked":""}}
                >
                آیا پیش نویس برگ خروج (فرم خروج از انبار) نیاز به تایید وصول مطالبات دارد؟


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
    </table>
</div>
