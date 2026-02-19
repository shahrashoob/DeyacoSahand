<form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf

    <div class="row">

        @include("utility.setting._radio_box",["key"=>"check_contour_with_time","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"cheek_allocation_amount_with_production_amount","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"warps_remaining_checked","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"check_material_flow_in_machine_allocation","label1"=>"بله","label0"=>"خیر"])




        @include("component.input._number",["id"=>$values["min_shrinkage_percent"]->key,"lable"=>$values["min_shrinkage_percent"]->caption,"value"=>$values["min_shrinkage_percent"]->double_value])
        @include("component.input._number",["id"=>$values["max_shrinkage_percent"]->key,"lable"=>$values["max_shrinkage_percent"]->caption,"value"=>$values["max_shrinkage_percent"]->double_value])


    </div>


    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

</form>
