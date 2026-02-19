@extends('layouts.admin._master')

@section('page_header_title',"داشبورد مدیریت پیمانکاران ")

@section('content')
    <div class="row">

        @include("contractor.admin.dashboard._allocation_info_small")
        @include("contractor.admin.dashboard._packing_form_list")
        @include("contractor.admin.dashboard._form_list")
        @include("contractor.public._log_list")

        <div class="col-md-12">
            <a href="{{route("contractor.admin.dashboard.view_card",$contractor_allocation->production)}}"
               class="btn btn-outline-dark">
                بازگشت
            </a>
        </div>
    </div>

@endsection

@section("scripts")
    <script>

    </script>
@endsection
