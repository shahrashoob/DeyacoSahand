    <form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="row">


        <div class="col-md-12">
            <br/>

            <b>{{$values["show_product_image"]->caption}}</b>:
            <input
                name="{{ $values["show_product_image"]->key}}"
                type="radio"
                {{$values["show_product_image"]->integer_value==1?"checked":""}}
                value="1"
            />فعال

            <input
                name="{{ $values["show_product_image"]->key}}"
                type="radio"
                {{$values["show_product_image"]->integer_value==0?"checked":""}}
                value="0"
            />غیرفعال
            <br/>
            <br/>
        </div>

        @include("utility.setting._radio_box",["key"=>"effective_inventory_in_order","label1"=>"بله","label0"=>"خیر"])

        @include("utility.setting._radio_box",["key"=>"is_there_a_sales_system","label1"=>"بله","label0"=>"خیر"])

        @include("component.input._radio_box01",["id"=>$values["receive_the_consumer_price_in_the_pricing"]->key,"label"=>$values["receive_the_consumer_price_in_the_pricing"]->caption,"label0"=>"خیر","label1"=>"بله","value"=>$values["receive_the_consumer_price_in_the_pricing"]->integer_value])

        @include("component.input._radio_box01",["id"=>$values["does_sales_view_inventory_on_the_way"]->key,"label"=>$values["does_sales_view_inventory_on_the_way"]->caption,"label0"=>"خیر","label1"=>"بله","value"=>$values["does_sales_view_inventory_on_the_way"]->integer_value])
        @include("component.input._radio_box01",["id"=>$values["does_sales_set_permission_for_inventory_on_the_way"]->key,"label"=>$values["does_sales_set_permission_for_inventory_on_the_way"]->caption,"label0"=>"خیر","label1"=>"بله","value"=>$values["does_sales_set_permission_for_inventory_on_the_way"]->integer_value])

        @include("component.input._radio_box01",["id"=>$values["method_of_calculating_active_inventory_on_sales"]->key,"label"=>$values["method_of_calculating_active_inventory_on_sales"]->caption,"label0"=>"موجودی کل بسته بندی ها","label1"=>"موجودی بسته بندی های مجاز","value"=>$values["method_of_calculating_active_inventory_on_sales"]->integer_value])
{{--        این تنظیمات در هیچ کجا اعمال نشده است و کنسل است.
 @include("component.input._radio_box01",["id"=>$values["type_of_status_id_after_confirm_product_request_form_in_permission"]->key,"label"=>$values["type_of_status_id_after_confirm_product_request_form_in_permission"]->caption,"label0"=>"محاسبه توسط سیستم","label1"=>"تحویل شده ( به صورت فورس ماژور)","value"=>$values["type_of_status_id_after_confirm_product_request_form_in_permission"]->integer_value])--}}


        @include("component.input._radio_box01",["id"=>$values["allow_get_shipping_method_in_buy"]->key,"label"=>$values["allow_get_shipping_method_in_buy"]->caption,"value"=>$values["allow_get_shipping_method_in_buy"]->integer_value])
        @include("component.input._radio_box01",["id"=>$values["allow_get_shipping_method_in_product_permission"]->key,"label"=>$values["allow_get_shipping_method_in_product_permission"]->caption,"value"=>$values["allow_get_shipping_method_in_product_permission"]->integer_value])


        <div class="col-md-12">
            <br/>

            <b>{{$values["order_loading_status_id"]->caption}}</b>:
            <input
                    name="{{ $values["order_loading_status_id"]->key}}"
                    type="radio"
                    {{$values["order_loading_status_id"]->integer_value==460000100?"checked":""}}
                    value="460000100"
            />بله

            <input
                    name="{{ $values["order_loading_status_id"]->key}}"
                    type="radio"
                    {{$values["order_loading_status_id"]->integer_value==460000200?"checked":""}}
                    value="460000200"
            />خیر
            <br/>
            <br/>
        </div>

        @include("component.input._textarea",["id"=>$values["reject_product_description"]->key,"lable"=>$values["reject_product_description"]->caption,"value"=>$values["reject_product_description"]->string_value])

        @include("component.input._number",["id"=>$values["percent_allow_to_reject_packing_form"]->key,"lable"=>$values["percent_allow_to_reject_packing_form"]->caption,"value"=>$values["percent_allow_to_reject_packing_form"]->integer_value])


        @include("component.input._radio_box01",["id"=>$values["allow_show_customer_caption_in_production_dashboard"]->key,"label"=>$values["allow_show_customer_caption_in_production_dashboard"]->caption,"value"=>$values["allow_show_customer_caption_in_production_dashboard"]->integer_value])

    </div>





    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

</form>
