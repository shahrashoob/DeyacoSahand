@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-8">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
{{--                    <h5> لیست وضعیت های کارت تولید <b> {{$goods_kind->caption}}</b>--}}
{{--                        <a class="btn btn-outline-success"--}}
{{--                           href="{{route("production.waiting_status.create",$goods_kind)}}"> <i--}}
{{--                                class="fa fa-plus"></i> افزودن وضعیت جدید </a>--}}
{{--                    </h5>--}}
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> کد وضعیت</th>
                                <th> عنوان وضعیت</th>
                                <td></td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($goods_kind->production_waiting_status as $item)
                                <tr>
                                    <td>{{++$row}} </td>
                                    <td>
                                        {{$item->status->getCode()}}
                                    </td>
                                    <td>
                                       {{$item->status->getCaption()}}
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <a class="btn btn-outline-dark"
                       href="{{route("line_product_station.goods_kind.index",$goods_kind)}}">بازگشت</a>

                </div>

            </div>
        </div>

    </div>

@endsection
