<div class="w-100"></div>
<div class="col-md-12">
    @include("component.input._checkbox",["id"=>"can_i_borrow_from_this_supplier",'label'=>"آیا می توان از این تامین کننده قرض گرفت","checked"=>$supplier->can_i_borrow_from_this_supplier??0])
</div>
<div class="w-100"></div>
<div class="col-md-6">
{{--    @include("component.input._aotocomplet2",[--}}
{{--        "id"=>"supplier_type_id",--}}
{{--        "label"=>"نوع تامین کننده ",--}}
{{--        "option"=>$supplier_type_option["items"],--}}
{{--        "val"=>$supplier_type_option["value"],--}}
{{--        "text"=>$supplier_type_option["text"],--}}
{{--        "class_col"=>""--}}
{{--        ])--}}
    @include("component.input._lable",["id"=>"supplier_type_id","label"=>"نوع تامین کننده","value"=>$employment->nationality_id==1?"تامین کننده داخل کشور":"تامین کننده خارج از کشور"])
</div>
@include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
            "id"=>"end_date_of_contract",
            'label'=>"تاریخ پایان قرارداد ",
            "value"=>"",
            "mark"=>"*"
            ])
