@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
{{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست   اشیاء هوشمند
                        <a class="btn btn-success" href="{{route("utility.smart_object.create")}}"> <i
                                class="fa fa-plus"></i> افزودن شیء جدید </a>

                        <a class="btn btn-primary" href="{{route("utility.smart_object.setting")}}"> <i
                                class="fa fa-cog"></i> تنظیمات </a>
                    </h5>
                    <div class="label float-right ">
                        <a class="btn btn-primary" href="{{url("upload/utility_files/app_entry.apk")}}">دانلود اپ  موبایل ورود و خروج</a>
                    </div>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> عنوان </th>
                                <th> نوع </th>
                                <th> IP </th>
                                <th> Port </th>
                                <th> توکن (Token) </th>
                                <th> مقدار کنتور اصلی </th>
                                <th> آخرین بروزرسانی </th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("utility.smart_object.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>{{$item->smart_object_type->caption??""}}</td>
                                    <td>{{$item->ip??""}}</td>
                                    <td>{{$item->port??""}}</td>
                                    <td>{{$item->token??""}}</td>
                                    <td>{{$item->contour??""}}</td>
                                    <td>{{$item->getLastUpdate()}}</td>
                                    <td>{{$item->status->caption??""}}</td>
                                    <td>
                                        <a href="{{route("utility.smart_object.destroy",$item)}}"
                                           onclick="return confirm('آیا از حذف شیء اطمینان دارید؟')"><i
                                                class="fa fa-trash text-danger"></i> </a>
                                    </td>
                                    <td>
                                        @if($item->ip) @endif
                                        <a href="{{route("utility.smart_object.check_connection",$item)}}"><i
                                                class="fa fa-eye text-primary"></i> </a>
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
