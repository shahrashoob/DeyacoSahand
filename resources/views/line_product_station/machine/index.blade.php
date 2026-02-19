@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست ماشین های
                        <b>
                            {{ $machine_type->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive center">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد ماشین</th>
                                <th>شماره ماشین</th>
                                <th> عنوان ماشین</th>
                                <td>وضعیت تولید</td>
                                <td>کانال تولید مجاز ماشین</td>
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
                                        {{$item->number_code}}
                                    </td>
                                    <td>
                                        <a href="{{$item->machine_type->active_status_id == 1200?route("line_product_station.machine.edit",$item):""}}">{{$item->caption}}</a>
                                    </td>
                                    <td>{{$item->production_status->caption??""}}</td>
                                    <td>
                                        <a href="{{ route(
                                            'line_product_station.machine.production_channel_type.index',
                                            $item
                                        ) }}">
                                            {{ $item->machine_production_channel_types()->where("machine_type_id",$machine_type->id)->count() }}
                                            کانال تولید
                                        </a>

                                    </td>
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
            <div>
                <a href="{{route("line_product_station.machine_type.index",$machine_type->station_id)}}" class="btn btn-outline-dark">بازگشت</a>

            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
