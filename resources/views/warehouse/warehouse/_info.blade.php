
<div class="row">
    @include("component.input._text",["id"=>"code",'label'=>"کد انبار","value"=>$warehouse->code,"class_col"=>"col-md-6"])
    <div class="w-100"></div>
    @include("component.input._text",["id"=>"caption",'label'=>"عنوان انبار ","value"=>$warehouse->caption,"class_col"=>"col-md-6"])
    <div class="w-100"></div>
    @include("component.input._text",["id"=>"ic",'label'=>"مرکز هزینه ","value"=>$warehouse->ic,"class_col"=>"col-md-6"])

    <div class="w-100"></div>
    @include("component.input._select",[
                                       "id"=>"shift_id",
                                       "label"=>"شیفت کاری ",
                                       "option"=>$shift_option["items"],
                                       "val"=>$shift_option["value"],
                                       "text"=>$shift_option["text"],
                                       "class_col"=>"col-md-6"
                                       ])


    <div class="w-100"></div>
    <div class="col-md-12">
        <br/>
        @include("component.input._checkbox_simple",["id"=>"cheek_loading_control_for_exist_form",'label'=>"آیا کنترل بارگیری برای فرم خروج از انبار چک شود؟ ","checked"=>$warehouse->cheek_loading_control_for_exist_form])
        @include("component.input._checkbox_simple",["id"=>"check_amount_product_in_entry",'label'=>"آیا مقدار کالا در زمان ورود به انبار چک شود؟ ","checked"=>$warehouse->check_amount_product_in_entry])

    
        <br/>
    </div>

    @include("component.input._radio_box01",["id"=>"allow_entry_with_pin","label"=>"آیا ورود به انبار می تواند با pin فرم های بسته بندی  انجام شود؟","label0"=>"خیر","label1"=>"بله","value"=>$warehouse->allow_entry_with_pin])

    @include("component.input._number",["id"=>"max_delivery_time","label"=>"حداکثر زمان ارسال بار (روز)","value"=>$warehouse->max_delivery_time])
    @include("component.input._number",["id"=>"earlier_delivery_time","label"=>"حداکثر زمان ارسال بار زودتر از موعد (روز)","value"=>$warehouse->earlier_delivery_time])


    <div class="col-md-12">
        <br/>
        @include("component.input._checkbox_simple",["id"=>"allow_select_partial_of_packing_in_output",'label'=>"آیا امکان انتخاب بخشی از کالا در داشبورد خروج از کالا امکان پذیر است؟","checked"=>$warehouse->allow_select_partial_of_packing_in_output])
        <br/>
    </div>

    @include("component.input.select2._select2",[
                                     "id"=>"exit_form_label_printing_type_ids",
                                     "label"=>"فرمت های مجاز برگ خروج",
                                     "option"=>$packing_type_label_printing_type_option["items"],
                                     "val"=>$packing_type_label_printing_type_option["value"],
                                     "text"=>$packing_type_label_printing_type_option["text"],
                                     "class_col"=>"col-md-6"
                                     ])

</div>
