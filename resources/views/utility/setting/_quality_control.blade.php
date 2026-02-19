<form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="row">

        @include("utility.setting._radio_box",["key"=>"has_grading_and_control","label1"=>"بله","label0"=>"خیر"])

        <div class="col-md-6">
            @include("component.input.select2._select2",[
                "id"=>$values["posts_allows_quality_control"]->key,
                "label"=>$values["posts_allows_quality_control"]->caption,
                "option"=>$posts_allows_quality_control_option["items"],
                "class_col"=>""
                ])
        </div>
    </div>





    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

</form>
