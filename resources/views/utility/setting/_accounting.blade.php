<form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="row">


        @include("component.input.datepicker._datepicker",["id"=>"financial_year_start","lable"=>" آغاز سال مالی (روز/ماه) ","formatDate"=>"MM/DD","formatDate_jalali"=>"%m/%d","value"=>$values["financial_year_start"]->string_value])
        @include("component.input.datepicker._datepicker",["id"=>"financial_year_end","lable"=>" پایان سال مالی (روز/ماه) ","formatDate"=>"MM/DD","formatDate_jalali"=>"%m/%d","value"=>$values["financial_year_end"]->string_value])
        @include("component.input._number",["id"=>$values["tax_calculation_percentage"]->key,"lable"=>$values["tax_calculation_percentage"]->caption,"value"=>$values["tax_calculation_percentage"]->integer_value])
    </div>





    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

</form>
