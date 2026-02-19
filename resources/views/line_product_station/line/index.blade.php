@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست خط های تولید
                        <a class="btn btn-success" href="{{route("line_product_station.line.create")}}"> <i
                                    class="fa fa-plus"></i> افزودن خط تولید جدید </a>
                        <a class="btn btn-primary"
                           href="{{route("line_product_station.production_method.index")}}">
                            انواع روش های تولید </a>

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد خط</th>
                                <th> عنوان خط</th>
                                <th> ایستگاه های کاری</th>
                                <th>انبارک خط تولید</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->getCode()}}
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.line.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>

                                        <a href="{{route("line_product_station.station.index",$item)}}">
                                            {{$item->station->where("active_status_id",1200)->count()}}
                                            ایستگاه فعال</a>
                                        @if($item->active_status_id == 1200)
                                            <a class="text-success"
                                               href="{{route("line_product_station.station.create",$item)}}"><i
                                                        class="fa fa-plus-circle"></i> افزودن </a>
                                        @endif
                                    </td>
                                    <td>

                                        <a href="{{route("line_product_station.line.warehouse_index",$item)}}">
                                            {{$item->warehouses->count()}}
                                            انبارک</a>
                                        @if($item->active_status_id == 1200)
                                            <a class="text-success"
                                               href="{{route("line_product_station.line.warehouse_create",$item)}}"><i
                                                        class="fa fa-plus-circle"></i> </a>
                                        @endif
                                    </td>
                                    <td>{{$item->active_status->caption??""}}</td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$list->firstItem()}}</b>
                        تا
                        <b>{{$list->lastItem()}}</b>
                        از
                        <b>{{$list->total()}}</b>
                        رکورد موجود
                    </div>
                </div>
                <div class="text-center">
                    {{$list->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
