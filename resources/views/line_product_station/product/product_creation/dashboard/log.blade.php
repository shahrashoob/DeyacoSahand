@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")
    <div class="row">

       @include("line_product_station.product.product_creation.dashboard._log")

        <div class="col-md-12 center">
            <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
               class="btn btn-outline-dark">بازگشت</a>
        </div>

    </div>

@endsection
