<form id="form1" action="{{route("utility.setting.update",["hr.setting.dashboard.index"])}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="row">

        <div class="col-md-12">
            <br/>

            <b>{{$values["show_efficiency_to_operator"]->caption}}</b>:
            <input
                    name="{{ $values["show_efficiency_to_operator"]->key}}"
                    type="radio"
                    {{$values["show_efficiency_to_operator"]->integer_value==1?"checked":""}}
                    value="1"
            /> بله

            <input
                    name="{{ $values["show_efficiency_to_operator"]->key}}"
                    type="radio"
                    {{$values["show_efficiency_to_operator"]->integer_value==0?"checked":""}}
                    value="0"
            />خیر
            <br/>
            <br/>
        </div>
        @include("component.input._number",["id"=>$values["max_of_post_for_user"]->key,"lable"=>$values["max_of_post_for_user"]->caption,"value"=>$values["max_of_post_for_user"]->integer_value])



        @include("component.input._number",["id"=>$values["qr_one_time_token_time"]->key,"lable"=>$values["qr_one_time_token_time"]->caption,"value"=>$values["qr_one_time_token_time"]->integer_value])
{{--        @include("component.input._number",["id"=>$values["legal_working_hours_in_minute"]->key,"lable"=>$values["legal_working_hours_in_minute"]->caption,"value"=>$values["legal_working_hours_in_minute"]->integer_value])--}}
        @include("component.input._number",["id"=>$values["repetitive_passing"]->key,"lable"=>$values["repetitive_passing"]->caption,"value"=>$values["repetitive_passing"]->integer_value])
        @include("component.input._number",["id"=>$values["max_time_allowed_for_percent_in_company"]->key,"lable"=>$values["max_time_allowed_for_percent_in_company"]->caption,"value"=>$values["max_time_allowed_for_percent_in_company"]->integer_value])
        @include("component.input._number",["id"=>$values["legal_leave_in_month"]->key,"lable"=>$values["legal_leave_in_month"]->caption,"value"=>$values["legal_leave_in_month"]->integer_value])
        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"auto_exit_option_algorithm",
                "label"=>$values["auto_exit_option_algorithm"]->caption,
                "option"=>$auto_exist_option_algorithm_option["items"],
                "text"=>$auto_exist_option_algorithm_option["text"],
                "val"=>$auto_exist_option_algorithm_option["value"],
                "class_col"=>""
                ])
            <br/>
            <h6>در صورتی که فردی بیش از مقدار " حداکثر مدت زمان مجاز حضور در سازمان"، در سازمان حضور داشته باشد، با یکی از الگوریتم های فوق رویداد خروج برای فرد ثبت می گردد</h6>
        </div>
        @include("component.input._radio_box01",["id"=>$values["classified_absence_from_regular_working_hours"]->key,"label"=>$values["classified_absence_from_regular_working_hours"]->caption,"value"=>$values["classified_absence_from_regular_working_hours"]->integer_value,"label0"=>"خیر","label1"=>"بله"])

        @include("component.input._text",["id"=>$values["name_of_work_medicine_doctor"]->key,  "label"=>$values["name_of_work_medicine_doctor"]->caption,"value"=>$values["name_of_work_medicine_doctor"]->string_value])
        @include("component.input._textarea",["id"=>$values["address_of_work_medicine_doctor"]->key,  "label"=>$values["address_of_work_medicine_doctor"]->caption,"value"=>$values["address_of_work_medicine_doctor"]->string_value])


        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
    </div>
</form>
