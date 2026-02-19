@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <form id="form1" action="{{route("line_product_station.production_method.update",$production_method)}}" method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> ویرایش روش تولید {{$production_method->getCode()}} </h5>
                    </div>
                    <div class="card-block">


                        <div class="row">
                            @include("component.input._textarea",["id"=>"description",'label'=>"توضیحات روش تولید","value"=>$production_method->description])
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-md-12">
                <a href="{{route("line_product_station.production_method.index")}}"
                   class="btn btn-outline-dark">بازگشت</a>

                <button type="submit" class="btn btn-primary"> ذخیره</button>
            </div>
        </div>
    </form>

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
                "description": "required",

            }

        });
    </script>
@endsection
