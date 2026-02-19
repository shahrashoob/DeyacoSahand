@extends('layouts.admin._master')

@section('page_header_title'," طراحی کالا ")

@section('content')

    @include("customer.tmp.product_creation._list")

@endsection
@section("styles")
    <script>


    </script>
    @include("component.input.datepicker._script")
@endsection
