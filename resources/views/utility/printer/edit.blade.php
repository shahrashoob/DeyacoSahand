@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش    {{$printer->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.printer.update",$printer)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان پرینتر ","value"=>$printer->caption])
                            @include("component.input._password",["id"=>"password",'label'=>"رمز پرینتر","value"=>$printer->password])
                            @include("component.input._text",["id"=>"width",'label'=>"عرض کاغذ پرینتر (پیکسل)","value"=>$printer->width])
                            @include("component.input._text",["id"=>"height",'label'=>"طول کاغذ پرینتر (پیکسل)","value"=>$printer->height])

                        </div>

                        <a href="{{route("utility.printer.index")}}" class="btn btn-outline-dark">بازگشت</a>

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
