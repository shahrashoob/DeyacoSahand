@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>لیست تغییر وضعیت ها - سفارش {{$order->code()}} </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>تاریخ و ساعت</th>
                                <th> اقدام کنننده</th>
                                <th> رویداد</th>
                                <th> وضعیت</th>
                                <td>توضیحات</td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($order->order_logs as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->get_datetime()}} </td>
                                    <td>{{$item->user->fullname()??""}}</td>
                                    <td>
                                        {{$item->event->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->status->caption}}
                                    </td>
                                    <td>{{$item->customer_message->text??""}}</td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>
            <div class="text-center">
                <a href="{{route("customer_group.order.index")}}" class="btn btn-dark">بازگشت</a>
            </div>

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
