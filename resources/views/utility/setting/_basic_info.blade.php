{{--<form id="form1" action="{{route("utility.setting.update")}}" method="post"--}}
{{--      novalidate="novalidate">--}}
{{--    @csrf--}}
    <div class="row">
        @include("component.input._lable",["id"=>"a","lable"=>$values["api_key"]->caption,"value"=>$values["api_key"]->string_value])


    </div>


    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
{{--    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>--}}

{{--</form>--}}
