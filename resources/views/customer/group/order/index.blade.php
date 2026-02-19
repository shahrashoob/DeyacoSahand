@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>لیست سفارش ها </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد سفارش</th>
                                <th> اولویت سفارش</th>
                                <th> وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        @if($item->status_id==304010)
                                            <a href="{{route("customer_group.buy.index",$item->id)}}">{{$item->code()}}</a>
                                        @else
                                            <a href="{{route("customer_group.order.show",$item->id)}}">{{$item->code()}}</a>
                                        @endif
                                    </td>
                                    <td>{{$item->priority->caption??""}}</td>
                                    <td>
                                        <a href="{{route("customer_group.order.log",$item->id)}}">{{$item->getStatus(1)}}</a>
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
