<div class="col-md-12">
    <div class="row">

        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"customer_type_id",
                "label"=>__("input.customer_type_id"),
                "option"=>$customer_type_option["items"],
                "val"=>$customer_type_option["value"],
                "text"=>$customer_type_option["text"],
                "class_col"=>""
                ])
        </div>

        <div class="w-100"></div>
        <div class="col-md-6">
            @include("component.input._aotocomplet2",[
                "id"=>"country_of_nationality_id",
                "label"=>__("input.country_of_nationality_id"),
                "option"=>$country_of_nationality_option["items"],
                "val"=>$country_of_nationality_option["value"],
                "text"=>$country_of_nationality_option["text"],
                "class_col"=>""
                ])
        </div>

        <div class="w-100" id="customer_type1" style="display: block">
            @include("component.input._text",["id"=>"firstname1","label"=>__("input.firstname"),"value"=>$customer->user->firstname??$request["firstname1"],"class_col"=>"col-md-6"])
            @include("component.input._text",["id"=>"lastname1","label"=>__("input.lastname"),"value"=>$customer->user->lastname??$request["lastname1"],"class_col"=>"col-md-6"])
            <div class="col-md-6">
                @include("component.input._select",[
                    "id"=>"gender_id1",
                    "label"=>__("input.gender_id"),
                    "option"=>$gender_option["items"],
                    "val"=>$gender_option["value"],
                    "text"=>$gender_option["text"],
                    "class_col"=>""
                    ])
            </div>

            @include("component.input.datepicker._datepicker",["id"=>"birth_date1","lable"=>__("input.birth_date"),"value"=>$customer->birth_date??$request["birth_date1"],"class_col"=>"col-md-6"])

            @include("component.input._number",["id"=>"national_code1","label"=>__("input.national_code"),"value"=>$customer->user->national_code??$request["national_code1"],"class_col"=>"col-md-6"])

        </div>

        <div class="w-100" id="customer_type2" style="display: none">
            @include("component.input._text",["id"=>"caption2","label"=>__("input.company_name"),"value"=>$customer->caption!=""?$customer->caption:$request["caption2"],"class_col"=>"col-md-6"])

            @include("component.input._text",["id"=>"firstname2","label"=>__("input.company_ceo_firstname"),"value"=>$customer->user->firstname??$request["firstname2"],"class_col"=>"col-md-6"])
            @include("component.input._text",["id"=>"lastname2","label"=>__("input.company_ceo_lastname"),"value"=>$customer->user->lastname??$request["lastname2"],"class_col"=>"col-md-6"])
            <div class="col-md-6">
                @include("component.input._select",[
                    "id"=>"gender_id2",
                    "label"=>__("input.company_ceo_gender_id"),
                    "option"=>$gender_option["items"],
                    "val"=>$gender_option["value"],
                    "text"=>$gender_option["text"],
                    "class_col"=>""
                    ])
            </div>

            @include("component.input.datepicker._datepicker",["id"=>"birth_date2","lable"=>__("input.company_ceo_birth_date"),"value"=>$customer->birth_date??$request["birth_date2"],"class_col"=>"col-md-6"])
            @include("component.input._number",["id"=>"national_code2","label"=>__("input.national_code2"),"value"=>$customer->user->national_code??$request["national_code2"],"class_col"=>"col-md-6"])

            @include("component.input._number",["id"=>"register_code2","label"=>__("input.register_code"),"value"=>$customer->register_code??$request["register_code2"],"class_col"=>"col-md-6"])

            @include("component.input._number",["id"=>"economic_number2","label"=>__("input.economic_number"),"value"=>$customer->economic_number??$request["economic_number2"],"class_col"=>"col-md-6"])

        </div>


    </div>
</div>







