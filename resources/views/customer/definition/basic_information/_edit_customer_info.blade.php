<div class="col-md-12">
    <div class="row">

        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"customer_type_id",
                "label"=>" نوع شخصیت   ",
                "option"=>$customer_type_option["items"],
                "val"=>$customer_type_option["value"],
                "text"=>$customer_type_option["text"],
                "class_col"=>""
                ])
        </div>

        <div class="w-100" id="customer_type1" style="display: block">
            @include("component.input._text",["id"=>"firstname1","label"=>"نام ","value"=>$customer->user->firstname??""])
            @include("component.input._text",["id"=>"lastname1","label"=>" نام خانوادگی ","value"=>$customer->user->lastname??""])
            <div class="col-md-6">
                @include("component.input._select",[
                    "id"=>"gender_id1",
                    "label"=>" جنسیت ",
                    "option"=>$gender_option["items"],
                    "val"=>$gender_option["value"],
                    "text"=>$gender_option["text"],
                    "class_col"=>""
                    ])
            </div>

            @include("component.input.datepicker._datepicker",["id"=>"birth_date1","lable"=>" تاریخ تولد ","value"=>$customer->birth_date??null])

            @include("component.input._text",["id"=>"caption1","label"=>"نام مستعار ","value"=>$customer->caption??""])
            @include("component.input._text",["id"=>"national_code1","label"=>"کد ملی ","value"=>$customer->user->national_code??""])

        </div>

        <div class="w-100" id="customer_type2" style="display: none">
            @include("component.input._text",["id"=>"caption2","label"=>"نام شرکت ","value"=>$customer->caption??""])

            @include("component.input._text",["id"=>"firstname2","label"=>"نام مدیر عامل ","value"=>$customer->user->firstname??""])
            @include("component.input._text",["id"=>"lastname2","label"=>" نام خانوادگی مدیرعامل ","value"=>$customer->user->lastname??""])
            <div class="col-md-6">
                @include("component.input._select",[
                    "id"=>"gender_id2",
                    "label"=>" جنسیت مدیر عامل ",
                    "option"=>$gender_option["items"],
                    "val"=>$gender_option["value"],
                    "text"=>$gender_option["text"],
                    "class_col"=>""
                    ])
            </div>

            @include("component.input.datepicker._datepicker",["id"=>"birth_date2","lable"=>" تاریخ تولد مدیرعامل ","value"=>$customer->birth_date??null])
            @include("component.input._text",["id"=>"national_code2","label"=>"شناسه ملی ","value"=>$customer->user->national_code??""])

            @include("component.input._number",["id"=>"register_code2","label"=>"شماره ثبت  ","value"=>$customer->register_code??""])

            @include("component.input._number",["id"=>"economic_number2","label"=>"شماره اقتصادی  ","value"=>$customer->economic_number??""])

        </div>








    </div>
</div>







