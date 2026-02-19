@extends('layouts.admin._master')

@section("page_header_title","داشبورد مدیریت کارت های تامین (کالای امانی)")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> کارت تامین (کالای امانی) {{$production->serial()}}</h5>
                </div>
                <div class="card-block">

                    @include("supplier.trust_product.dashboard._info_small")

                    <a href="{{route("supplier.trust_product.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>


                    @if( $production->waiting_status_id != 7011003)
                        <a href="{{route("supplier.trust_product.trust_product_allocation.index",$production)}}"
                           class="btn btn-primary">تخصیص مشتری </a>


                        <a href="{{route("supplier.trust_product.trust_product_allocation.terminate",$production)}}" onclick="return confirm('آیا از خاتمه یافته کردن کارت تامین (کالای امانی) اطمینان دارید؟')"
                           class="btn btn-danger">خاتمه یافته کردن</a>
                    @endif
                </div>
            </div>
        </div>

        @include("supplier.trust_product.dashboard._allocation")


    </div>

@endsection

@section("scripts")
    <script>

    </script>
@endsection
