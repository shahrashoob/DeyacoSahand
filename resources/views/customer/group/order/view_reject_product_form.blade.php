@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان - سفارش   ".$order->code())

@section('content')
    @include("customer.group.order._reject_product_form",[
    "route_path"=>"customer_group.order.view_reject_product_form",
    "back_route_path"=>"customer_group.order.show",
    "customer_reject"=>1
    ])

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

@endsection
