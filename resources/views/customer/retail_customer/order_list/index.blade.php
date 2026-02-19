@extends('layouts.admin._master')
@section("page_header_title","داشبورد فروش ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
{{--            @include("sales.public._search_view",["route"=>"sales.dashboard.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست سفارش ها</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد سفارش</th>
                                <th> وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("customer_group.retail_customer.order_list.view",$item->id)}}">{{$item->code()}}</a>
                                    </td>

                                    <td>
                                        {{$item->getStatus(1)}}
{{--                                        <a href="{{route("sales.dashboard.log",$item->id)}}">{{$item->getStatus(1)}}</a>--}}
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
@endsection
