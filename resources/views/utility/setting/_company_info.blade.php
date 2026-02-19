<form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="row">

        @include("component.input._select",[
                   "id"=>$values["company_country_id"]->key,
                   "label"=>$values["company_country_id"]->caption,
                   "option"=>$company_country_option["items"],
                    "class_col"=>"col-md-6"
                 ])
        <div class="w-100"></div><br/>
        @include("component.input._select",[
               "id"=>$values["company_province_id"]->key,
               "label"=>$values["company_province_id"]->caption,
               "option"=>$company_province_option["items"],
                 "class_col"=>"col-md-6"
               ])
        <div class="w-100"></div><br/>
        @include("component.input._text",["id"=>$values["company_city_name"]->key,"lable"=>$values["company_city_name"]->caption,"value"=>$values["company_city_name"]->string_value])
        @include("component.input._text",["id"=>$values["company_address"]->key,"lable"=>$values["company_address"]->caption,"value"=>$values["company_address"]->string_value])
        @include("component.input._number",["id"=>$values["company_postal_code"]->key,"lable"=>$values["company_postal_code"]->caption,"value"=>$values["company_postal_code"]->string_value])
        @include("component.input._number",["id"=>$values["company_phone_number"]->key,"lable"=>$values["company_phone_number"]->caption,"value"=>$values["company_phone_number"]->string_value])
        @include("component.input._text",["id"=>$values["company_location"]->key,"lable"=>$values["company_location"]->caption,"value"=>$values["company_location"]->string_value])

        @include("component.input._number",["id"=>$values["post_id_for_contract"]->key,"lable"=>$values["post_id_for_contract"]->caption,"value"=>$values["post_id_for_contract"]->integer_value])

    </div>


    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره</button>

</form>
