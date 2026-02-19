
<div class="table-responsive">
    <table class="table table-styling">

        <tr>
            <th></th>
            <th>
                پست جهت اطلاع رسانی
            </th>
        </tr>
        @if(!$data_supplier_default_setting->exit_form_require_quality_permission->enable)
            <tr>
                <td>
                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_require_quality_permission",
                                            "label"=>"آیا برگ خروج از انبار نیاز به تایید کنترل کیفیت دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>$data_supplier_default_setting->exit_form_require_quality_permission->value])

                </td>
                <td>

                </td>
            </tr>
        @endif
        @if(!$data_supplier_default_setting->exit_form_require_draft_permission->enable)
            <tr>
                <td>


                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_require_draft_permission",
                                            "label"=>"   آیا برگ خروج از انبار نیاز به تایید پیش نویس (واحد مالی) دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>$data_supplier_default_setting->exit_form_require_draft_permission->value])
                </td>
                <td>
                    <div class="col-md-12">
                        @include("component.input._select_simple",[
                                            "id"=>"exit_form_require_draft_permission_post_id",
                                            "option"=>$post_option_list["exit_form_require_draft_permission_post_id"]["items"],
                                            "val"=>$post_option_list["exit_form_require_draft_permission_post_id"]["value"],
                                            "text"=>$post_option_list["exit_form_require_draft_permission_post_id"]["text"],
                                            "class_col"=>"",
                                            "readonly"=>$data_supplier_default_setting->exit_form_require_draft_permission->enable ? 'readonly' : ''
                                            ])
                    </div>
                </td>
            </tr>
        @endif
        @if(!$data_supplier_default_setting->exit_form_require_permission->enable)
            <tr>
                <td>

                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_require_permission",
                                            "label"=>" آیا برگ خروج از انبار نیاز به تایید نهایی (واحد مالی) دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>$data_supplier_default_setting->exit_form_require_permission->value])


                </td>
                <td>
                    <div class="col-md-12">
                        @include("component.input._select_simple",[
                                            "id"=>"exit_form_require_permission_post_id",
                                            "option"=>$post_option_list["exit_form_require_permission_post_id"]["items"],
                                            "val"=>$post_option_list["exit_form_require_permission_post_id"]["value"],
                                            "text"=>$post_option_list["exit_form_require_permission_post_id"]["text"],
                                            "class_col"=>"",
                                              "readonly"=>$data_supplier_default_setting->exit_form_require_permission->enable ? 'readonly' : ''
                                            ])
                    </div>
                </td>
            </tr>
        @endif
        @if(!$data_supplier_default_setting->exit_form_loading_require_permission->enable)
            <tr>
                <td>


                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_loading_require_permission",
                                            "label"=>"آیا برگ خروج از انبار نیاز به ارسال دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>$data_supplier_default_setting->exit_form_loading_require_permission->value])
                </td>
                <td>

                </td>
            </tr>
        @endif
        @if(!$data_supplier_default_setting->exit_form_guarding_require_permission->enable)
            <tr>
                <td>


                    @include("component.input._radio_box01",[
                                            "id"=>"exit_form_guarding_require_permission",
                                            "label"=>"آیا برگ خروج از انبار نیاز به تایید نگهبانی دارد؟",
                                            "label0"=>"خیر","label1"=>"بله",
                                            "value"=>$data_supplier_default_setting->exit_form_guarding_require_permission->value])
                </td>
                <td>
                    <div class="col-md-12">
                        @include("component.input._select_simple",[
                                            "id"=>"exit_form_guarding_require_permission_post_id",
                                            "option"=>$post_option_list["exit_form_guarding_require_permission_post_id"]["items"],
                                            "val"=>$post_option_list["exit_form_guarding_require_permission_post_id"]["value"],
                                            "text"=>$post_option_list["exit_form_guarding_require_permission_post_id"]["text"],
                                            "class_col"=>"",
                                              "readonly"=>$data_supplier_default_setting->exit_form_require_permission->enable ? 'readonly' : ''
                                            ])
                    </div>
                </td>
            </tr>
        @endif
        {{--        <tr>--}}
        {{--            <td>5</td>--}}
        {{--            <td>--}}

        {{--                <input type="checkbox" id="checking_carrier_at_delivery_of_product"--}}
        {{--                       name="checking_carrier_at_delivery_of_product" {{$supplier->checking_carrier_at_delivery_of_product?"checked":""}}--}}
        {{--                >--}}
        {{--                آیا   کد بسته بندی / حامل در زمان تایید تحویل کالا توسط تامین چک شود؟--}}


        {{--            </td>--}}
        {{--            <td>--}}

        {{--            </td>--}}
        {{--        </tr>--}}
    </table>
</div>
