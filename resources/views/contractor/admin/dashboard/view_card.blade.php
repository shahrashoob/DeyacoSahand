@extends('layouts.admin._master')

@section('page_header_title',"داشبورد مدیریت پیمانکاران ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> دستور پیمان {{$production->serial()}}</h5>
                </div>
                <div class="card-block">

                    @include("contractor.admin.dashboard._info_small")

                    @if(isset($back_url_type) && $back_url_type=="production.dashboard.index")
                        <a href="{{route("production.dashboard.list")}}" class="btn btn-outline-dark">بازگشت</a>

                    @else
                        <a href="{{route("contractor.admin.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                    @endif

                    <a href="{{route("production.dashboard.print_card",$production)}}" class="btn btn-info">پرینت کردن
                        کارت</a>

                    @if( $production->waiting_status_id != 7008005)
                        <a href="{{route("contractor.admin.contractor_allocation.index",$production)}}"
                           class="btn btn-primary">تخصیص پیمانکار </a>


                        <a href="{{route("contractor.admin.contractor_terminate.index",$production)}}" onclick="return confirm('آیا از خاتمه یافته کردن دستور پیمان اطمینان دارید؟')"
                           class="btn btn-danger">خاتمه یافته کردن</a>
                    @endif
                </div>
            </div>
        </div>

        @include("contractor.admin.dashboard._allocation")
        @include("production.public.log_status")

    </div>

@endsection

@section("scripts")
    <script>

    </script>
@endsection
