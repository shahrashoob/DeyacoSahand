@extends('layouts.admin._master')

@section('page_header_title',"کارتابل جاری تولید ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> کارت تولید {{$production->serial()}}</h5>
                </div>
                <div class="card-block">

                    @include("production.production_card._info_small")

                    <a href="{{route("production.list")}}" class="btn btn-outline-dark">بازگشت</a>
                    <a href="{{route("production.print_card",$production)}}" class="btn btn-info">پرینت کردن
                        کارت</a>

                    @if($production->status->id!=500)


                        @if($production->status->id==520 && \Auth::user()->posts->first()->checkButtonPermission("production.edit"))
                            <a href="{{route("production.edit",$production)}}" class="btn btn-warning">ویرایش کارت
                                تولید</a>

                        @endif

                    @else


                        <a href="{{route("production.form1",$production)}}" class="btn btn-success"> ثبت کارت </a>
                        <a href="{{route("production.replace",$production)}}" class="btn btn-primary"
                           onclick="return confirm('آیا از جایگزین کردن کارت اطمینان دارید')">جایگزین کردن کارت</a>

                        @if( \Auth::user()->posts->first()->checkButtonPermission("production.cancel"))
                            <a href="{{route("production.cancel",$production)}}" class="btn btn-danger">کنسل کردن </a>
                        @endif
                    @endif


                </div>
            </div>
        </div>

    </div>

@endsection
