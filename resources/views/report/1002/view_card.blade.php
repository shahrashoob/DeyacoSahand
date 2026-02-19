@extends('layouts.admin._master')

@section('page_header_title',"گزارش 1002 - کارت های ثبت شده ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> کارت تولید {{$production->serial()}}</h5>
                </div>
                <div class="card-block">

                    @include("production.dashboard.production_card._info_small")



                    <a href="{{route("report.1002.index")}}" class="btn btn-outline-dark">بازگشت</a>
                    <a href="{{route("production.print_card",$production)}}" class="btn btn-info">پرینت کردن
                        کارت</a>




                </div>
            </div>
        </div>

                @include("production.public.log_status")

    </div>

@endsection
