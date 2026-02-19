@extends('layouts.admin._master')
@section("page_header_title","کارتابل جاری بازرگانی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("production.production_card._search_view",["route"=>"wh.cd.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست فرم های سفارش خرید کالا</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>شماره </th>
                                <th>کد کالا </th>
                                <th>نام کالا </th>
                                <th>تعداد  </th>
                                <th>تاریخ  </th>
                                <th>وضعیت  </th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                        <td>
                                            <a href="{{route("purchase.purchase.view_details",$item->id)}}" >{{$item->code()??""}}</a>
                                        </td>
                                        <td>{{$item->product->code}}</td>
                                        <td>{{$item->product->caption}}</td>
                                        <td>{{$item->amount}} {{$item->product->unit->caption}}</td>
                                        <td>{{$item->get_created_date()}}</td>
                                        <td>{{$item->status->caption??""}}</td>
                                   
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
                <div class="text-center" >
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
