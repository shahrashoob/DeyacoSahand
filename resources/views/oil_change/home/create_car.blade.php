@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت تعویض روغنی")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>  {{ $customer->shop_name }}

                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("oil_change.home.store_car",[$car,$current_km])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <table style="margin: auto ">
                                   <tr>
                                       <td colspan="5">
                                           @include("oil_change.home._pluck")
                                       </td>
                                   </tr>
                                    <tr>
                                        <td colspan="5" style="border: none">
                                            <br/>
                                            <br/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">
                                            @include("component.input._text",["id"=>"firstname",'label'=>"نام ","class_col"=>"col-md-12","autofocus"=>1])

                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">
                                            @include("component.input._text",["id"=>"lastname",'label'=>"نام خانوادگی ","class_col"=>"col-md-12","autofocus"=>1])

                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">
                                            @include("component.input._number",["id"=>"mobile",'label'=>"تلفن همراه ","class_col"=>"col-md-12","autofocus"=>1])

                                        </td>
                                    </tr>

                                </table>
                            </div>

                            <div class="col-md-12" style="text-align: center">
                                <br/>
                                <br/>
                                <a href="{{route("oil_change.home.index")}}" class="btn btn-lg btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary btn-lg">ثبت ماشین</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div>

            </div>
        </div>

    </div>

@endsection
@section("styles")
    <link rel="stylesheet" href="{{asset('oil_change/css/pluck.css')}}">
    <style>
        .custom-select, .form-control{
            padding:4px 0px 3px !important;
            text-align: center;
        }
        select.form-control:not([size]):not([multiple]){
            height: auto;
        }
    </style>
@endsection

@section("scripts")
    <script src="{{asset('assets/plugins/jquery-validation-1.11.1/localization/messages_fa2.js')}}"></script>

    <script>

        $('#form1').validate({
            rules: {
                firstname: "required",
                lastname: "required",
                mobile: {required:true,minlength: 11, maxlength: 11},
            }
        });

    </script>
@endsection
