<div class="row">
    <div class="col-md-12">
        @include("component.input._lable",["id"=>"caption",'label'=>"عنوان حامل","value"=>$carrier_type->caption??""])
        <div class="col-md-6">
            @include("component.input._lable",["id"=>"carrier_group_id","label"=>" گروه حامل  ",  "value"=>$carrier_type->carrier_group->caption??"","class_col"=>"" ])
        </div>
        @include("component.input._lable",["id"=>"unit_id", "label"=>"واحد سنجش کالای حامل ",  "value"=>$carrier_type->unit->caption??"","class-col"=>""])
        @include("component.input._lable",["id"=>"min_band_lable",'label'=>"حداقل باند","value"=>$carrier_type->min_band_number??""])
        @include("component.input._lable",["id"=>"max_band_lable",'label'=>"حداکثر باند","value"=>$carrier_type->max_band_number??""])

        @include("component.input._lable",["id"=>"min_band_capacity",'label'=>"حداقل ظرفیت هر باند","value"=>$carrier_type->min_band_capacity??""])
        @include("component.input._lable",["id"=>"max_band_capacity",'label'=>"حداکثر ظرفیت هر باند","value"=>$carrier_type->max_band_capacity??""])

        @include("component.input._lable",["id"=>"average_weight",'label'=>"میانگین وزن حامل ها (کیلوگرم)","value"=>$carrier_type->average_weight??""])
        @include("component.input._lable",["id"=>"length",'label'=>"  طول  (میلیمتر)","value"=>$carrier_type->length??""])
        @include("component.input._lable",["id"=>"width",'label'=>" عرض  (میلیمتر) ","value"=>$carrier_type->width??""])
        @include("component.input._lable",["id"=>"height",'label'=>" ارتفاع (میلیمتر)","value"=>$carrier_type->height??""])

        @include("component.input._lable",["id"=>"it_changes_volume_after_filling",'label'=>"آیا نوع حامل پس از تکمیل تغییر حجم دارد؟","value"=>$carrier_type->it_changes_volume_after_filling?"بله":"خیر"])


    </div>
</div>