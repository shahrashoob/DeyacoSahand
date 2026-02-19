@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")

    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>خدمات رفاهی / کیف پول تارا

                    </h5>
                </div>

            </div>
        </div>
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-body"><h6 class="mb-4">صورت حساب تارا</h6>
                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>شناسه تراکنش</th>
                                <th>شماره حساب</th>
                                <th>مبلغ تراکنش</th>
                                <th>نوع تراکنش</th>
                                <th>زمان انجام تراکنش</th>
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

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>


                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <br/>
                    <a href="{{route("accounting.welfare_service.tara.client.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                </div>
            </div>
        </div>
    </div>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
