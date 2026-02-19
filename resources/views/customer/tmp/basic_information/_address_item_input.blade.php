<div class="col-md-4">
    @include("component.input._aotocomplet2",[
        "id"=>"country_id",
        "label"=>__("input.country_id"),
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
        "label"=>__("input.province_id"),
        "option"=>$province_option["items"],
        "val"=>$province_option["value"],
        "text"=>$province_option["text"],
        "class_col"=>""
        ])
</div>
@include("component.input._text",["id"=>"city_name", "lable"=>__("input.city_name"),"value"=>$address->city_name??$request["city_name"],"class_col"=>"col-md-4"])
<div class="w-100"></div>
@include("component.input._text",["id"=>"phone", "lable"=>__("input.phone"),"value"=>$address->phone??$request["phone"],"class_col"=>"col-md-4"])
<div class="w-100"></div>
@include("component.input._hidden",["id"=>"mobile", "lable"=>__("input.mobile (without zero)"),"readonly"=>1,"value"=>$mobile,"class_col"=>"col-md-4"])
<div class="w-100"></div>
@include("component.input._text",["id"=>"postal_code", "lable"=>__("postal_code"),"value"=>$address->postal_code??$request["postal_code"],"class_col"=>"col-md-4"])
<div class="w-100"></div>
@include("component.input._textarea",["id"=>"address", "lable"=>__("address"),"value"=>$address->address??$request["address"]])
<div class="w-100"><br/></div>
