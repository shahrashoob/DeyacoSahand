@extends('layouts.admin._master')
@section('page_header_title',"داشبورد طراحی کالا")
@section("content")



    @include("line_product_station.product.product_creation.product_show._tabs",["tab"=>"edit_material_flow","info"=>isset($info)?$info:null])


@endsection
@section("styles")

@endsection

@section("scripts")

@endsection
