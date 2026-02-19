@extends('customer.tmp.layouts.admin._master')
@section("page_header_title",__("user panel"))
@section("content")
    <div class="row">


        @include("customer.tmp.product_creation._log")


        <div class="col-md-12 center">
            <a href="{{route("dashboard")}}"
               class="btn btn-outline-dark">{{__("btn.back")}}</a>
        </div>

    </div>

@endsection
