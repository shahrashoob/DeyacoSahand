@extends('layouts.admin._master')
@section("page_header_title","کارتابل منابع انسانی")
@section("content")

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">


                    <h5>اطلاعات
                        <span>{{$employment->worker->firstname}} {{$employment->worker->lastname}}</span>

                    </h5>

                </div>
                @include("hr.worker.personal_file._tab")
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <a href="{{route('hr.worker.index')}}"
           class="btn btn btn-outline-dark ">بازگشت</a>
    </div>

@endsection




