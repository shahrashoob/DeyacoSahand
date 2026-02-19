<form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf

    <div class="row">

        @include("component.input._text",["id"=>$values["product_creation_unit_address"]->key,"lable"=>$values["product_creation_unit_address"]->caption,"value"=>$values["product_creation_unit_address"]->string_value])
        @include("component.input._text",["id"=>$values["product_creation_unit_phone"]->key,"lable"=>$values["product_creation_unit_phone"]->caption,"value"=>$values["product_creation_unit_phone"]->string_value])


    </div>

    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

</form>
