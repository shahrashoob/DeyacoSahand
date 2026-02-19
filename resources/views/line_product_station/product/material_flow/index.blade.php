@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")



    @include("line_product_station.product._tabs",["tab"=>"edit_material_flow","info"=>isset($info)?$info:null])


@endsection
@section("styles")

@endsection

@section("scripts")

@endsection
