<form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf

    <div class="row">
        @include("utility.setting._radio_box",["key"=>"send_sms_in_create_production","label1"=>"بله","label0"=>"خیر"])


        @include("component.input._select",["id"=>$values["send_sms_in_create_allocation_machine_to_post_id1"]->key,"lable"=>$values["send_sms_in_create_allocation_machine_to_post_id1"]->caption,"option"=>$send_sms_in_create_allocation_machine_to_post_id1_option["items"],"class_col"=>"col-md-6"])

        @include("utility.setting._radio_box",["key"=>"property1_show_in_production_dashboard","label1"=>"بله(مقدار مشخصه)","label0"=>"خیر","label2"=>"بله (رنگ مشخصه)"])
        @include("utility.setting._radio_box",["key"=>"property2_show_in_production_dashboard","label1"=>"بله(مقدار مشخصه)","label0"=>"خیر","label2"=>"بله (رنگ مشخصه)"])
        @include("utility.setting._radio_box",["key"=>"production_channel_type_show_in_production_dashboard","label1"=>"بله","label0"=>"خیر"])
        @include("component.input._radio_box01",["id"=>$values["dashboard_type_of_machines"]->key,"label"=>$values["dashboard_type_of_machines"]->caption,"value"=>$values["dashboard_type_of_machines"]->integer_value,"label0"=>"پیش فرض","label1"=>"داشبورد ستونی نوع 1"])


        <br/>
<br/>
{{--        حذف شده--}}
{{--        @include("component.input._number",["id"=>$values["diff_of_production_and_allocation_in_the_end_of_production"]->key,"lable"=>$values["diff_of_production_and_allocation_in_the_end_of_production"]->caption,"value"=>$values["diff_of_production_and_allocation_in_the_end_of_production"]->integer_value])--}}
{{--        <br/>--}}
{{--        @include("component.input._number",["id"=>$values["max_diff_of_production_and_allocation_in_the_end_of_production"]->key,"lable"=>$values["max_diff_of_production_and_allocation_in_the_end_of_production"]->caption,"value"=>$values["max_diff_of_production_and_allocation_in_the_end_of_production"]->integer_value])--}}

    </div>


    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

</form>
