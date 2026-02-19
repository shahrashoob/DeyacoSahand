@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت کارت های تامین (کالای امانی)")

@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("supplier.trust_product.dashboard._search_view",["route"=>"supplier.trust_product.dashboard.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست کارت های تامین (کالای امانی)</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>سریال کارت تامین</th>
                                <th>شماره سفارش</th>
                                <th>مشتری</th>
                                <th>نام کالا</th>
                                <th>تعداد</th>
                                <th>واحد</th>
                                <th>اولویت</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>{{$item->production_type_id}}
                                        <a href="{{route("supplier.trust_product.dashboard.view_card",$item->production_card_id)}}">
                                            {{$item->serial(1)}}
                                        </a>
                                    </td>
                                    <td>
                                        {{$item->order?$item->order->code():""}}
                                    </td>
                                    <td>
                                        {{$item->order?$item->order->customer->caption??"":""}}
                                    </td>
                                    <td>{{$item->product->code." - ".$item->product->caption}}</td>
                                    <td>{{$item->number}}</td>
                                    <td>{{$item->product->unit->bach_caption}}</td>
                                    <td>{{$item->priority->caption??""}}</td>
                                    <td>{{$item->getStatus()}}</td>


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
@section("scripts")
    <script>


    </script>
@endsection

