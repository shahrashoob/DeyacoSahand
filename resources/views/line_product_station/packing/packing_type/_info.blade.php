
    @include("component.input._text",["id"=>"caption",'label'=>"نام نوع بسته بندی","value"=>$packing_type->caption??""])


    @include("component.input._text",["id"=>"label_caption","label"=>"عنوان لیبل (system= نام سامانه)","value"=>$packing_type->label_caption??""])
    @include("component.input._number",["id"=>"weight","label"=>"وزن بسته بندی بدون حامل  وکالا(کیلوگرم)","value"=>$packing_type->weight??""])

    <div class="w-100"></div>
    <div class="col-md-6">
        @include("component.input._aotocomplet2",[
            "id"=>"packing_type_label_printing_type_id",
            "label"=>" قالب لیبل بسته بندی ",
            "option"=>$label_packing_type_option["items"],
            "val"=>$label_packing_type_option["value"],
            "text"=>$label_packing_type_option["text"],
            "class_col"=>""
            ])
    </div>
    <div class="w-100"></div>
    <div class="col-md-6">
        @include("component.input._aotocomplet2",[
            "id"=>"printer_unit_display_type_id",
            "label"=>" نوع نمایش واحد کالا در قالب",
            "option"=>$printer_unit_display_type_option["items"],
            "val"=>$printer_unit_display_type_option["value"],
            "text"=>$printer_unit_display_type_option["text"],
            "class_col"=>""
            ])
    </div>

    @include("component.input._number",["id"=>"max_amount_of_production_form_separately","label"=>"حداکثر مقدار هر آیتم  فرم تولید در زمان استخراج به صورت مجزا","value"=>$packing_type->max_amount_of_production_form_separately??"0"])
    @include("component.input._number",["id"=>"max_row_to_display_sub_packing_in_print","label"=>"حداکثر ردیف برای نمایش بسته بندی های فرعی در پرینت","value"=>$packing_type->max_row_to_display_sub_packing_in_print??"0"])

    @include("component.input._number",["id"=>"weight_error_percentage","label"=>"درصد خطای وزن بسته بندی","value"=>$packing_type->weight_error_percentage??""])


    @include("component.input._number",["id"=>"length",'label'=>"  طول  (میلیمتر)","value"=>$packing_type->length??""])
    @include("component.input._number",["id"=>"width",'label'=>" عرض  (میلیمتر) ","value"=>$packing_type->width??""])
    @include("component.input._number",["id"=>"height",'label'=>" ارتفاع (میلیمتر)","value"=>$packing_type->height??""])


    <div class="w-100"></div>
    <div class="col-md-6">
        @include("component.input._aotocomplet2",[
            "id"=>"discharge_type_id",
            "label"=>" نوع تخلیه ",
            "option"=>$discharge_type_option["items"],
            "val"=>$discharge_type_option["value"],
            "text"=>$discharge_type_option["text"],
            "class_col"=>""
            ])
    </div>
    <div class="w-100"></div>
    <div class="col-md-6">
        @include("component.input._checkbox",["id"=>"many_degrees_can_fit_into_one",'label'=>"آیا یک کالا با درجه های متفاوت میتوانند داخل بسته بندی قرار بگیرد؟","checked"=>$packing_type->many_degrees_can_fit_into_one??0])
    </div>
    <div class="w-100"></div>
    <div class="col-md-6">
        @include("component.input._checkbox",["id"=>"it_is_possible_extract_production_form_separately",'label'=>"آیا می توان آیتم های فرم تولید را به صورت مجزا با این بسته بندی استخراج نمود؟","checked"=>$packing_type->it_is_possible_extract_production_form_separately??0])
    </div>

    <div class="w-100"></div>
    <div class="col-md-6">
        @include("component.input._checkbox",["id"=>"create_sub_packing_form_in_creation",'label'=>"آیا بسته بندی های فرعی در زمان ایجاد بسته بندی تعریف شوند؟","checked"=>$packing_type->create_sub_packing_form_in_creation??0])
    </div>

