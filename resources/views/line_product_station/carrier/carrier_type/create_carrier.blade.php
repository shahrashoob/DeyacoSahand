@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن  حامل جدید از نوع {{$carrier_type->caption}}   </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.carrier.carrier_type.store_carrier",$carrier_type)}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        @include("component.input._text",["id"=>"code",'label'=>"شماره حامل","value"=>""])
                        @include("component.input._number",["id"=>"weight",'label'=>"وزن (کیلوگرم)","value"=>"0"])


                       <div class="col-md-12">
                           <a href="{{route("line_product_station.carrier.carrier_type.carrier_list",$carrier_type)}}" class="btn btn-outline-dark">بازگشت</a>

                           <button type="submit" class="btn btn-primary">  ذخیره تغییرات </button>
                       </div>

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
                "code": "required",
                "weight":"required"
            }
        });
    </script>
@endsection
