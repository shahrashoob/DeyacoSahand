@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")

    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>خدمات رفاهی / پرسنل

                    </h5>
                </div>
                <div class="card-body">
                    <form id="form1" action="{{route("accounting.welfare_service.tara.admin.dashboard.add_user")}}"
                          method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"user_id",
                                    "label"=>" نام شاغل ",
                                    "option"=>$worker_option["items"],
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>""
                                    ])
                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary"> ثبت فرد جدید</button>

                    </form>

                </div>

            </div>
        </div>
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <h5>لیست پرسنل ثبت نام شده

                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>نام و نام خانوادگی</th>
                                <th>کد ملی</th>
                                <th>شماره موبایل</th>
                                <th>شماره حساب تارا</th>
                                <th>تاریخ ثبت نام</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php

                                $row=1;
                            @endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        {{$item->worker->fullname()}}
                                    </td>
                                    <td>
                                        {{$item->national_code}}
                                    </td>
                                    <td>
                                        {{$item->mobile}}
                                    </td>
                                    <td>
                                        {{$item->account_number}}
                                    </td>
                                    <td>
                                        {{$item->create_datetime()}}
                                    </td>
                                    <td>
                                        <a href="{{route("accounting.welfare_service.tara.admin.dashboard.charge",$item)}}"
                                           class="">افزایش شارژ</a> &
                                        <a href="{{route("accounting.welfare_service.tara.admin.dashboard.decharge",$item)}}"
                                           class="">کاهش شارژ</a>


                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <br/>
                    {{--                    <a href="{{route("accounting.welfare_service.tara.admin.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>--}}

                </div>
            </div>
        </div>
    </div>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
