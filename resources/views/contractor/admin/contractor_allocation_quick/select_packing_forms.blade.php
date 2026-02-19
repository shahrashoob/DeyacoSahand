@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])

@section('page_header_title',"داشبورد مدیریت پیمانکاران ")

@section('content')
    <div class="row">

        <div class="col-sm-12">


            @include("contractor.admin.contractor_allocation._select_packing")
        </div>
        <div class="col-md-12 center" id="btn_return_list">
            <br/>
            <a href="{{route("contractor.admin.dashboard.index")}}"
               class="btn btn-outline-dark     " type="button">
                بازگشت
            </a>

            @if(isset($production_channel_type))
                <a href="{{route("contractor.admin.contractor_allocation_quick.show_selected_packing",[$contractor,$production_channel_type])}}"
                   class="btn btn-primary" type="button">
                    تایید و ادامه
                </a>
            @endif


        </div>


    </div>
@endsection

@section("styles")

@endsection

@section("scripts")

@include("contractor.admin.contractor_allocation._script")

@endsection

