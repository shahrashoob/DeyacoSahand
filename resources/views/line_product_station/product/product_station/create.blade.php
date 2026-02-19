@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن ایستگاه کاری جدید به {{$product_route->caption}} ({{$product->fullCaption()}}) </h5>
                </div>
                <div class="card-block">


                    <div class="row">
                        <div class="col-sm-12">
                            <form id="form1"
                                  action="{{route("line_product_station.product.product_station.store",[$product,$product_route,$product_creation_process])}}"
                                  method="post"
                                  autocomplete="off"
                                  novalidate="novalidate">
                                @csrf
                                <div class="row">

                                   @include("line_product_station.product.product_station._info")
                                </div>
                                @include("line_product_station.product.product_station._btn_list")

                            </form>
                        </div>
                    </div>



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
    @include("line_product_station.product.product_station._script")

@endsection
