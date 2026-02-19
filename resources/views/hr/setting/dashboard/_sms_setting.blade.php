<form id="form1" action="{{route("utility.setting.update",["hr.setting.dashboard.index"])}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="row">

        @include("utility.setting._radio_box",["key"=>"hr_sing_in_sms","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"hr_sign_out_sms","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"hr_leave_confirm_replace_sms","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"hr_leave_conform_parent_sms","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"hr_leave_set_comment_replace_sms","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"hr_overtime_conform_parent_sms","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"hr_mission_conform_parent_sms","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"hr_replacement_confirm_parent_sms","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"hr_replacement_confirm_replace_sms","label1"=>"بله","label0"=>"خیر"])

        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
    </div>
</form>
