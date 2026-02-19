@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش قراداد
                        {{$contract->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route('accounting.contract.contract.update',$contract)}}" method="post"
                          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان قراداد ","value"=>$contract->caption])
                            @include("component.input._number",["id"=>"number_of_contract",'label'=>"تعداد نسخه قراداد","value"=>$contract->number_of_contract])
                            @if(count($existing_contract)>0)
                            @include("component.input._aotocomplet2",[
                               "id"=>"active_status_id",
                                "label"=>" وضعیت   ",
                                "option"=>$active_status_option["items"],
                                "val"=>$contract->active_status->id??"",
                                "text"=>$contract->active_status->caption??"",
                                 ])
                            @endif
                            @include("component.input._textarea",["id"=>"guide",'label'=>"راهنمای تایید قراداد ","value"=>$contract->guide??""])

                            @include("component.input._textarea",["id"=>"description",'label'=>"توضیحات ","value"=>$contract->description??""])


                        </div>
                        <br/>
                        <a href="{{route('accounting.contract.contract.index')}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary">ذخیره</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "contract_type_id": "required",
                "number_of_contract":"required",
                "guide":"required",
            }
        });
    </script>
@endsection
