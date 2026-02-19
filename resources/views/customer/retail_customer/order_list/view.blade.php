@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  فروش "." سفارش:".$order->code())

@section('content')

    <div class="row">


        @include("customer.group.buy._order_factor_products")



        @include("sales.dashboard._exist_form_list")


        <div class="col-md-12">
            <a href="{{route("customer_group.retail_customer.order_list.index",$order)}}" class="btn btn-outline-dark">
                بازگشت
            </a>

        </div>
    </div>
@endsection


@section("modals")



@endsection

@section("scripts")


@endsection

@section("styles")
@endsection

