@extends('layouts.admin._master',["keypress_enable"=>1])

@section("content")
    <div class="row">
        <div class="col-md-12">
            <form id="form1" action="{{route("utility.smart_object.submit_setting")}}" method="post"
                  novalidate="novalidate">
                @csrf
            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات اشیاء هوشمند</h5>
                </div>

                <div class="carb-block" >


                            @include("component.input._text",["id"=>$values["smart_object_server_ip"]->key,"lable"=>$values["smart_object_server_ip"]->caption,"value"=>$values["smart_object_server_ip"]->string_value])
                            @include("component.input._text",["id"=>$values["smart_object_server_port"]->key,"lable"=>$values["smart_object_server_port"]->caption,"value"=>$values["smart_object_server_port"]->string_value])


                      <div class="col-md-12">
                          <a href="{{route("utility.smart_object.index")}}" class="btn btn-outline-dark">بازگشت</a>

                          <button type="submit" class="btn btn-primary">  ذخیره تغییرات </button>
                      </div>


                </div>
            </div>
            </form>
        </div>
    </div>
@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
