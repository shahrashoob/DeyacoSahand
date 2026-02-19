@extends('layouts.admin._master')
@section("page_header_title","داشبورد فروش - سفارش   ".$order->code())

@section('content')
    @include("customer.group.order._reject_product_form",["route_path"=>"sales.dashboard.view_reject_product_form", "back_route_path"=>"sales.dashboard.view_order"])

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

@endsection
