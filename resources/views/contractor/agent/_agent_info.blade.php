<div class="col-md-12">
    <div class="row">


        @include("component.input._text",["id"=>"firstname","label"=>"نام ","value"=>$agent->worker->firstname ??""])
        @include("component.input._text",["id"=>"lastname","label"=>" نام خانوادگی ","value"=>$agent->worker->lastname??""])
        @include("component.input._text",["id"=>"email","label"=>"نام کاربری  ","value"=>$agent->worker->email??""])
        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"agent_type_id",
                "label"=>"نوع ارتباط",
                "option"=>$agent_type_option["items"],
                "val"=>$agent_type_option["value"],
                "text"=>$agent_type_option["text"],
                "class_col"=>""
                ])

            <br/>

            @include("component.input._select",[
                "id"=>"gender_id",
                "label"=>" جنسیت ",
                "option"=>$gender_option["items"],
                "val"=>$gender_option["value"],
                "text"=>$gender_option["text"],
                "class_col"=>""
                ])
            <div class="w-100"><br/></div>
            @include("component.input._select",[
                "id"=>"country_id",
                "label"=>"کشور",
                "option"=>$country_option["items"],
                "val"=>$country_option["value"],
                "text"=>$country_option["text"],
                "class_col"=>""
                ])
        </div>
        <div class="w-100"><br/></div>
            @include("component.input.datepicker._datepicker",["id"=>"date_of_birth","lable"=>" تاریخ تولد ","value"=>$agent->worker->date_of_birth??""])
        @include("component.input._text",["id"=>"birth_certificate_number","label"=>"شماره شناسنامه","value"=>$agent->worker->birth_certificate_number??""])
            @include("component.input._text",["id"=>"national_code","label"=>"کد ملی ","value"=>$agent->worker->national_code??""])
        @include("component.input._number",["id"=>"mobile","label"=>"موبایل ","value"=>$agent->worker->mobile??""])

        <div class="w-100"></div>
        <div class="col-md-12">
            @include("component.input._checkbox",["id"=>"has_the_right_to_sign",'label'=>"آیا حق امضا دارد؟","checked"=>$agent->has_the_right_to_sign??0])
        </div>



    </div>
</div>



