<form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="row">

        @include("component.input._text",["id"=>$values["transport_loading_header_text"]->key,"lable"=>$values["transport_loading_header_text"]->caption,"value"=>$values["transport_loading_header_text"]->string_value])
        @include("component.input._number",["id"=>$values["max_show_packing_form_code_in_special_license"]->key,"lable"=>$values["max_show_packing_form_code_in_special_license"]->caption,"value"=>$values["max_show_packing_form_code_in_special_license"]->integer_value])
        @include("component.input._number",["id"=>$values["transport_item_label_type"]->key,"lable"=>$values["transport_item_label_type"]->caption,"value"=>$values["transport_item_label_type"]->integer_value])

    </div>





    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

</form>
