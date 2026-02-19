<form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="row">

        @include("component.input._textarea",["id"=>$values["text_footer_per_factor"]->key,"lable"=>$values["text_footer_per_factor"]->caption,"value"=>$values["text_footer_per_factor"]->string_value])



        @include("utility.setting._radio_box",["key"=>"show_product_caption_in_pre_factor","label1"=>"بله","label0"=>"خیر"])

        @include("utility.setting._radio_box",["key"=>"show_packing_type_caption_in_pre_factor","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"show_packing_type_code_in_pre_factor","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"show_property_1_in_pre_factor","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"show_property_2_in_pre_factor","label1"=>"بله","label0"=>"خیر"])
        @include("utility.setting._radio_box",["key"=>"show_property_3_in_pre_factor","label1"=>"بله","label0"=>"خیر"])



    </div>





    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

</form>
