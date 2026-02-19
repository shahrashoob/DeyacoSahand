@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت مشتریان")
@section("content")
        <div class="row">
            <div class="col-md-12">

                    <div class="card">
                        <div class="card-header">
                            <h5>لیست نمایندگان
                                <a href="{{route("contractor.agent.create")}}"
                                   class="btn btn-outline-success">افزودن نماینده</a></h5>
                        </div>
                        <div class="card-block">
                            @include("contractor.agent._agent_list")
                        </div>
            </div>
@endsection