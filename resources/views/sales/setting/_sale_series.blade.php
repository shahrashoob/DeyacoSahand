<div class="col-sm-12 ">
    <form id="form1" action="{{route("utility.setting.update",["sales.setting.index"])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
@csrf

@include("component.input._number",["id"=>$values["sale_series_type_1"]->key,"lable"=>$values["sale_series_type_1"]->caption,"value"=>$values["sale_series_type_1"]->integer_value])
@include("component.input._number",["id"=>$values["sale_series_type_2"]->key,"lable"=>$values["sale_series_type_2"]->caption,"value"=>$values["sale_series_type_2"]->integer_value])

        <div class="col-md-12" style="text-align: center" id="button_list">
            <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>

            <button type="submit" class="btn btn-primary">
                ذخیره تغییرات
            </button>

        </div>
    </form>
</div>
