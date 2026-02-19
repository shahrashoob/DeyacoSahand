@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">


        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>ویرایش     {{$goods_kind_property->caption}}
                    </h5>
                </div>
                <form id="form1" action="{{route("line_product_station.goods_kind.property.update",[$goods_kind,$goods_kind_property])}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate">
                    @csrf
                    <div class="card-block">
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان مشخصه ","value"=>$goods_kind_property->caption])

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"field_type_id",
                                    "label"=>"نوع فیلد ",
                                    "option"=>$field_type_option["items"],
                                    "val"=>$field_type_option["value"],
                                    "text"=>$field_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"special_unit_id",
                                    "label"=>"واحد اندازه گیری ",
                                    "option"=>$special_unit_option["items"],
                                    "val"=>$special_unit_option["value"],
                                    "text"=>$special_unit_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"parent_id",
                                    "label"=>"مشخصه وابسته ",
                                    "option"=>$dependent_property_option["items"],
                                    "val"=>$dependent_property_option["value"],
                                    "text"=>$dependent_property_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"status_id",
                                    "label"=>"وضعیت",
                                    "option"=>$status_option["items"],
                                    "val"=>$status_option["value"],
                                    "text"=>$status_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            @include("component.input._number",["id"=>"priority_number",'label'=>"اولویت نمایش ","value"=>$goods_kind_property->priority_number])
                        </div>
                        <a class="btn btn-dark" href="{{route("line_product_station.goods_kind.property.index",$goods_kind)}}"> بازگشت </a>

                        <button type="submit" class="btn btn-success">ذخیره</button>

                    </div>
                </form>

            </div>

        </div>
        <div class="col-md-12">

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        select {
            width: 150px;
        }
    </style>
@endsection


@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "priority_number": "required",
                "field_type_id_auto": "required",
            }
        });

    </script>
@endsection

