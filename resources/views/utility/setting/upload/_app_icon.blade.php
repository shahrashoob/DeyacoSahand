<form id="form1" action="{{route("utility.setting.update_logo")}}" method="post" enctype="multipart/form-data">
@csrf
<div class="row">
    @include("component.input._hidden",["id"=>"file_name","value"=>"favicon.ico"])
    <div class="col-md-6">
        <div class="row">
            @include("component.input._file_upload",["id"=>"logo_file","label"=>"تصویر Favicon ( 50*50 پیکسل)","value"=>""])

            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">آپلود</button>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <img style="width: 100px" src="{{asset("assets/images/favicon.ico")}}"/>
    </div>

</div>
<br/>
<br/>


</form>
