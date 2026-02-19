<div class="row">

    @include("component.input._text",["id"=>"caption",'label'=>"عنوان حامل","value"=>$carrier_type->caption??"","readonly"=>true])

        @include("component.input._text",[
            "id"=>"carrier_group_id",
            "label"=>" گروه حامل  ",
            "value"=>$carrier_group_option["text"],
            "readonly"=>true
            ])

    <div class="w-100"></div>
    <div class="col-md-6">
        @include("component.input._aotocomplet2",[
            "id"=>"unit_id",
            "label"=>"واحد سنجش کالای حامل ",
            "option"=>$unit_option["items"],
            "val"=>$unit_option["value"],
            "text"=>$unit_option["text"],
            "class_col"=>""
            ])
    </div>

    @include("component.input._number",["id"=>"min_band_number",'label'=>"حداقل باند","value"=>$carrier_type->min_band_number??"","readonly"=>true])
    @include("component.input._number",["id"=>"max_band_number",'label'=>"حداکثر باند","value"=>$carrier_type->max_band_number??"","readonly"=>true])

    @include("component.input._number",["id"=>"min_band_capacity",'label'=>"حداقل ظرفیت هر باند","value"=>$carrier_type->min_band_capacity??"","readonly"=>true])
    @include("component.input._number",["id"=>"max_band_capacity",'label'=>"حداکثر ظرفیت هر باند","value"=>$carrier_type->max_band_capacity??"","readonly"=>true])

    @include("component.input._number",["id"=>"average_weight",'label'=>"میانگین وزن حامل ها (کیلوگرم)","value"=>$carrier_type->average_weight??"","readonly"=>true])
    @include("component.input._number",["id"=>"length",'label'=>"  طول  (میلیمتر)","value"=>$carrier_type->length??"","readonly"=>true])
    @include("component.input._number",["id"=>"width",'label'=>" عرض  (میلیمتر) ","value"=>$carrier_type->width??"","readonly"=>true])
    @include("component.input._number",["id"=>"height",'label'=>" ارتفاع (میلیمتر)","value"=>$carrier_type->height??"","readonly"=>true])

    @include("component.input._text",["id"=>"product_code",'label'=>"کد کالای حامل","value"=>$carrier_type->product_code??""])

    <div class="col-md-12">
        @include("component.input._checkbox",["id"=>"placed_in_warehouse",'label'=>"آیا این نوع حامل می تواند در انبار قرار بگیرد؟","checked"=>$carrier_type->placed_in_warehouse??false])
    </div>

    <div class="col-md-12">
        @include("component.input._checkbox",["id"=>"has_number_ability",'label'=>"آیا این نوع حامل قابلیت شماره گذاری دارد؟","checked"=>$carrier_type->has_number_ability??false])
    </div>

    <div class="col-md-12">
        @include("component.input._checkbox",["id"=>"system_can_define_new_carrier",'label'=>"آیا سیستم می تواند حامل جدید تعریف کند؟","checked"=>$carrier_type->system_can_define_new_carrier??false])
    </div>
    <div class="col-md-12">
        @include("component.input._checkbox",["id"=>"it_changes_volume_after_filling",'label'=>"آیا نوع حامل پس از تکمیل تغییر حجم دارد؟","checked"=>$carrier_type->it_changes_volume_after_filling??false,"disabled"=>true])
    </div>

</div>
