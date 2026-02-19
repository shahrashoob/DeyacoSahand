<div class="col-md-4">
    @include("component.input._aotocomplet2",[
        "id"=>"country_id",
        "label"=>"کشور ",
        "option"=>$country_option["items"],
        "val"=>$country_option["value"],
        "text"=>$country_option["text"],
        "class_col"=>""
        ])
</div>

<div class="w-100"></div>
<div class="col-md-4">
    @include("component.input._aotocomplet2",[
        "id"=>"province_id",
        "label"=>"استان ",
        "option"=>$province_option["items"],
        "val"=>$province_option["value"],
        "text"=>$province_option["text"],
        "class_col"=>""
        ])
</div>
@include("component.input._text",["id"=>"city_name", "lable"=>"شهرستان","value"=>$address->city_name??"","class_col"=>"col-md-4"])
<div class="w-100"></div>
@include("component.input._text",["id"=>"phone", "lable"=>"شماره ثابت / نمابر ","value"=>$address->phone??"","class_col"=>"col-md-4"])

@include("component.input._text",["id"=>"mobile", "lable"=>"شماره همراه (بدون صفر) ","value"=>$address->mobile??"","class_col"=>"col-md-4"])
<div class="w-100"></div>
@include("component.input._text",["id"=>"postal_code", "lable"=>"کد پستی","value"=>$address->postal_code??"","class_col"=>"col-md-4"])
<div class="w-100"></div>
@include("component.input._textarea",["id"=>"address", "lable"=>"نشانی ","value"=>$address->address??""])
<div class="w-100"><br/></div>
