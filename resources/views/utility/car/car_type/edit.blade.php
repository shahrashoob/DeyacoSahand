@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش  {{$car_type->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.car.car_type.update",$car_type)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
{{--                            @include("component.input._text",["id"=>"caption",'label'=>"نام خودرو","value"=>$car_type->caption])--}}
                            @include("component.input._text",["id"=>"min_weight",'label'=>"حداقل وزن","value"=>$car_type->min_weight])
                            @include("component.input._text",["id"=>"max_weight",'label'=>"حداکثر وزن","value"=>$car_type->max_weight])
                            @include("component.input._text",["id"=>"min_volume",'label'=>"حداقل حجم","value"=>$car_type->min_volume])
                            @include("component.input._text",["id"=>"max_volume",'label'=>"حداکثر حجم","value"=>$car_type->max_volume])


                        </div>

                        <a href="{{route("utility.car.car_type.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

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
                "min_weight": "required",
                "max_weight": "required",
                "max_volume": "required",
                "min_volume": "required",
            }
        });
    </script>
@endsection
