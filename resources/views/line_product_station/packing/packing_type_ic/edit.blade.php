@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت/بروز رسانی اطلاعات بسته بندی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <form id="form1"
                  action="{{route("line_product_station.packing.packing_type_ic.update",$packing_type)}}"
                  method="post"
                  novalidate="novalidate">
                @csrf
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-header">
                                <h5> اطلاعات نوع بسته بندی در سامانه منظومه داده ای </h5>
                            </div>
                            <div class="card-block">
                                @include('line_product_station.packing.packing_type_ic._list_in_ic')
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>
                                    اطلاعات نوع بسته بندی در
                                    {{$software_name}}
                                </h5>
                            </div>
                            <div class="card-block">
                                @include('line_product_station.packing.packing_type_ic._list')

                            </div>
                        </div>
                    </div>
                </div>


                <div class="center">
                    <a href="{{route("line_product_station.packing.packing_type.index")}}"
                       class="btn btn-outline-dark ">بازگشت</a>

                    <button type="submit" class="btn btn-primary"> بروزرسانی</button>
                </div>
            </form>
        </div>

    </div>
@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection


