@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش    {{$financial_software->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.financial_software.definition.update",$financial_software)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"database_name",'label'=>"نام دیتابیس  ","value"=>$financial_software->database_name])
                            @include("component.input._password",["id"=>"username",'label'=>"نام کاربری","value"=>$financial_software->username])
                            @include("component.input._password",["id"=>"password",'label'=>"کلمه عبور","value"=>$financial_software->password])
                            @include("component.input._text",["id"=>"server_url",'label'=>"آدرس API","value"=>$financial_software->server_url])

                        </div>

                        <a href="{{route("utility.financial_software.definition.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary">  ذخیره تغییرات </button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "ip": "required",
                "status_id_auto": "required",
            }
        });
    </script>
@endsection
