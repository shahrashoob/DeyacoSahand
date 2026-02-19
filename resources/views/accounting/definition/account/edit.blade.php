@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش  حساب  {{$account->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("accounting.definition.account.update",$account)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._number",["id"=>"code",'label'=>"کد حساب","value"=>$account->code])
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان حساب ","value"=>$account->caption])

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"has_separator_in_full_code",'label'=>"آیا در کد زیر حساب جدا کننده وجود دارد؟","checked"=>$account->has_separator_in_full_code])
                            </div>

                        </div>

                        <a href="{{route("accounting.definition.account.index")}}" class="btn btn-outline-dark">بازگشت</a>

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
                "code": "required",
                "status_id_auto": "required",
            }
        });
    </script>
@endsection
