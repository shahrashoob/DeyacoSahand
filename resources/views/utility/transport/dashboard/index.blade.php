@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
             @include("utility.transport.dashboard._search_view",["route"=>"utility.transport.dashboard.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست سفارش ها
                    </h5>

                    <a href="{{route("utility.transport.dashboard.create_transport")}}" class="btn btn-primary">ثبت
                        ارسال بار (ویژه دوره پیاده سازی) </a>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد</th>
                                <th>شماره سفارش</th>
                                <th>نام مشتری</th>
                                <th> تعداد بسته بندی حمل و نقل</th>
                                <th>تاریخ ایجاد</th>
                                <th>وضعیت</th>
                                <th>گزارش به<br/> تفکیک بسته بندی</th>
                                <th>گزارش به<br/> تفکیک کد کالا</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("utility.transport.dashboard.transport_item",$item)}}">
                                        {{$item->getCode()}}
                                        </a>
                                    </td>
                                    <td>

                                            {{isset($item->order)?$item->order->code():""}}

                                    </td>
                                    <td>
                                        {{$item->customer_caption}}
                                    </td>
                                    <td>
                                        {{$item->items()->count()}}
                                    </td>
                                    <td>
                                        {{$item->create_datetime()}}
                                    </td>
                                    <td>{{$item->status->caption}}</td>
                                    <td>
                                        <a href="{{route("utility.transport.dashboard.download_report2",$item)}}"><i
                                                class="fa fa-download"></i> </a>
                                    </td>
                                    <td>
                                        <a href="{{route("utility.transport.dashboard.download_report1",$item)}}"> <i
                                                class="fa fa-download"></i> </a>
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
    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

@endsection
