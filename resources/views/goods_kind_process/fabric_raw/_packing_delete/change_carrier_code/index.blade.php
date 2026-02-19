@extends('layouts.admin._master')

@section('page_header_title',"داشبورد بسته بندی  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تغییر شماره حامل فرم بسته بندی {{$packing_form->code}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route($route_path."submit",$packing_form)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate"
                          style="display: inline"
                    >
                        @csrf
                        <div class="alert alert-info">
                            در صورت اطمینان از تغییر شماره حامل، لطفا یک شماره حامل جدید که خالی می باشد را وارد نمایید.
                        </div>
                        <div class="w-100"></div>
                        <div class="col-md-6">
                            @include("component.input._aotocomplet2",[
                                "id"=>"carrier_type_id",
                                "label"=>"نوع حامل ",
                                "option"=>$carrier_type_option["items"],
                                "val"=>$carrier_type_option["value"],
                                "text"=>$carrier_type_option["text"],
                                "class_col"=>""
                                ])
                        </div>
                        @include("component.input._text",["id"=>"carrier_id","lable"=>"شماره حامل جدید","value"=>""])

                        <a href="{{route("fabric_raw.packing.dashboard.view",$packing_form)}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary" onclick="return confirm('آیا از تغییر شماره حامل اطمینان دارید؟')">ذخیره اطلاعات</button>
                    </form>
                </div>
            </div>
        </div>


    </div>

@endsection

@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "carrier_type_id_auto": "required",
                "carrier_id": "required",
            }
        })
    </script>
@endsection
