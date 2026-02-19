@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])

@section('page_header_title',"داشبورد مدیریت پیمانکاران ")

@section('content')
    <div class="row">

        <div class="col-sm-12">


            @include("contractor.admin.contractor_allocation._select_packing")
        </div>
        <div class="col-md-12 center">
            <br/>
            <a href="{{route("contractor.admin.contractor_allocation.index",$production)}}"
               class="btn btn-outline-dark     " type="button">
                بازگشت
            </a>


        </div>


    </div>
@endsection

@section("styles")

@endsection

@section("scripts")

    @include("contractor.admin.contractor_allocation._script")

@endsection

