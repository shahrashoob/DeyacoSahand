
@include("component.input._text",["id"=>"p1",'label'=>"نام نوع بسته بندی","value"=>$packing_type->caption??"","readonly"=>1])


@include("component.input._text",["id"=>"label_caption","label"=>"عنوان لیبل (system= نام سامانه)","value"=>$packing_type->label_caption??""])
@include("component.input._number",["id"=>"p2","label"=>"وزن بسته بندی بدون حامل(کیلوگرم)","value"=>$packing_type->weight??"","readonly"=>1])

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

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"normal_amount_unit_type_id",
        "label"=>" نوع واحد کالا برای محاسبه مقدار نرمال بسته بندی ",
        "option"=>$normal_operation_unit_type_option["items"],
        "val"=>$normal_operation_unit_type_option["value"],
        "text"=>$normal_operation_unit_type_option["text"],
        "class_col"=>"",
        ])
</div>

@include("component.input._number",["id"=>"max_amount_of_production_form_separately","label"=>"حداکثر مقدار هر آیتم  فرم تولید در زمان استخراج به صورت مجزا","value"=>$packing_type->max_amount_of_production_form_separately??"0"])
@include("component.input._number",["id"=>"max_row_to_display_sub_packing_in_print","label"=>"حداکثر ردیف برای نمایش بسته بندی های فرعی در پرینت","value"=>$packing_type->max_row_to_display_sub_packing_in_print??"0"])

@include("component.input._number",["id"=>"p3","label"=>"درصد خطای وزن بسته بندی","value"=>$packing_type->weight_error_percentage??"","readonly"=>1])


@include("component.input._number",["id"=>"p4",'label'=>"  طول  (میلیمتر)","value"=>$packing_type->length??"","readonly"=>1])
@include("component.input._number",["id"=>"p5",'label'=>" عرض  (میلیمتر) ","value"=>$packing_type->width??"","readonly"=>1])
@include("component.input._number",["id"=>"p6",'label'=>" ارتفاع (میلیمتر)","value"=>$packing_type->height??"","readonly"=>1])


@include("component.input._number",["id"=>"normal_amount",'label'=>"مقدار نرمال بسته بندی","value"=>$packing_type->normal_amount,"readonly"=>0])



<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"active_status_id",
        "label"=>" وضعیت  فعال بودن ",
        "option"=>$status_option["items"],
        "val"=>$packing_type->active_status->id??"",
        "text"=>$packing_type->active_status->caption??"",
        "class_col"=>""
        ])
</div>

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._aotocomplet2",[
        "id"=>"discharge_type_id",
        "label"=>" نوع تخلیه ",
        "option"=>$discharge_type_option["items"],
        "val"=>$discharge_type_option["value"],
        "text"=>$discharge_type_option["text"],
        "class_col"=>"",
        ])
</div>
<div class="w-100"></div>


@include("component.input._textarea",["id"=>"note",'label'=>"دستور العمل برگشت موارد اولیه","value"=>$packing_type->note??"","class_col"=>"col-md-9","height"=>"70px"])



<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._checkbox",["id"=>"packaging_forms_include_brand",'label'=>"آیا بسته بندی شامل لوگو می باشد؟","checked"=>$packing_type->packaging_forms_include_brand??0,])
</div>

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._checkbox",["id"=>"take_amount_from_parent_production_card",'label'=>"آیا مقدار برند را از کارت سطح بالا می گیرید؟","checked"=>$packing_type->take_amount_from_parent_production_card??0,])
</div>





<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._checkbox",["id"=>"p7",'label'=>"آیا یک کالا با درجه های متفاوت میتوانند داخل بسته بندی قرار بگیرد؟","checked"=>$packing_type->many_degrees_can_fit_into_one??0,"disabled"=>1])
</div>
<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._checkbox",["id"=>"it_is_possible_extract_production_form_separately",'label'=>"آیا می توان آیتم های فرم تولید را به صورت مجزا با این بسته بندی استخراج نمود؟","checked"=>$packing_type->it_is_possible_extract_production_form_separately??0])
</div>

<div class="w-100"></div>
<div class="col-md-6">
    @include("component.input._checkbox",["id"=>"p8",'label'=>"آیا بسته بندی های فرعی در زمان ایجاد بسته بندی تعریف شوند؟","checked"=>$packing_type->create_sub_packing_form_in_creation??0,"disabled"=>1])
</div>

