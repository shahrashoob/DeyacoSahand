@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن دسته جدید به طبقه {{$goods_kind_classification->caption}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.goods_kind.classification.option.store",$goods_kind_classification)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان دسته ","value"=>""])
                            <div class="w-100"></div>
                        </div>

                        <a href="{{route("line_product_station.goods_kind.classification.option.index",$goods_kind_classification)}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت دسته جدید</button>

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
                "caption": "required", "goods_kind_id_auto": "required",
            }
        });
    </script>
@endsection
