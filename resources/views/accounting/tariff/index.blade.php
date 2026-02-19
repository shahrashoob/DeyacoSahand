@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست تعرفه ها
                        @if($post_user->checkButtonPermission("accounting.tariff.add_new"))
                            <a class="btn btn-success" href="{{route("accounting.tariff.create")}}"> <i
                                    class="fa fa-plus"></i> افزودن تعرفه </a>
                        @endif
                    </h5>
                </div>
                <div class="card-block">
                    @include("accounting.tariff._search_view",["route"=>"accounting.tariff.index",])

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد</th>
                                <th> عنوان تعرفه</th>
                                <td>واحد پول</td>
                                <td>تاریخ شروع</td>
                                <td>تاریخ پایان</td>
                                <td>وضعیت</td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->id}}</td>
                                    <td>
                                        @if($post_user->checkButtonPermission("accounting.tariff.edit_info"))
                                            <a href="{{route("accounting.tariff.edit",$item)}}">{{$item->caption}}</a>
                                        @else
                                            {{$item->caption}}
                                        @endif
                                    </td>
                                    <td>{{$item->currency->caption}}</td>
                                    <td>
                                        {{$item->start_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->end_datetime()}}
                                    </td>
                                    <td>{{$item->status->caption}}</td>
                                    <td>
                                        @if($post_user->checkButtonPermission("accounting.tariff.upload_list"))
                                            <a href="{{route("accounting.tariff.upload",$item)}}">
                                                <i class="fa fa-upload"></i> آپلود لیست
                                            </a>
                                        @endif
                                        @if($post_user->checkButtonPermission("accounting.tariff.view_log"))
                                            <a class="text-warning" href="{{route("accounting.tariff.view_log",$item)}}">
                                                <i class="fa fa-eye"></i>مشاهده سابقه
                                            </a>
                                        @endif
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
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
