@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>ویرایش حداقل و حداکثر مشخصه {{$goods_kind_property->caption}}
                    </h5>
                </div>
                <form id="form1" action="{{route("line_product_station.goods_kind.option.update_number",$goods_kind_property)}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate">
                    @csrf
                    <div class="card-block">

                        <div class="row">
                            @include("component.input._number",["id"=>"min_value",'label'=>"حداقل مقدار ","value"=>$goods_kind_property->min_value])
                            @include("component.input._number",["id"=>"max_value",'label'=>"حداکثر مقدار ","value"=>$goods_kind_property->max_value])

                        </div>
                        <a class="btn btn-dark" href="{{route("line_product_station.goods_kind.property.index",$goods_kind_property->goods_kind_id)}}"> بازگشت </a>
                        <button type="submit" class="btn btn-success">ذخیره</button>

                    </div>
                </form>

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
                "min_value": "required",
                "max_value": "required",
            }
        });

    </script>
@endsection

