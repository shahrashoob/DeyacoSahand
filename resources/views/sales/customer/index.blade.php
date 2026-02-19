@extends('layouts.admin._master')
@section("page_header_title","داشبورد جاری فروش  / ثبت سفارش برای مشتری")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("sales.customer._search_view",["route"=>"sales.customer.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست مشتریان ها</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد مرکز</th>
                                <th>نام مرکز</th>
                                <th>کانال توزیع</th>
                                <th> استان</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->code??""}}</td>
                                    <td>{{$item->caption??""}}</td>
                                    <td>{{$item->channelType->caption??""}}</td>
                                    <td>{{$item->province->caption??""}}</td>
                                    <td>
                                        <a class="text-success" href="{{route("sales.customer.orders",$item)}}">
                                            <i class="fa fa-list"></i> لیست سفارش ها
                                        </a>
                                        <a href="{{route("sales.customer.new_order_for_customer",$item)}}">
                                            <i class="fa fa-plus"></i> ثبت سفارش جدید
                                        </a>


                                            <a href="{{route("sales.product_request_permission.index_customer",$item)}}"><i
                                                        class="fa fa-shopping-basket "></i>  سفارشات مشتری در یک نگاه
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
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
