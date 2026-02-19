@extends('layouts.admin._master')
@section("page_header_title","داشبورد جاری انبار مواد اولیه")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("production.dashboard.production_card._search_view",["route"=>"wh.material.list"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست کارت های در انتظار تحویل کالا / تایید تحویل کالا</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>سریال تولید</th>
                                <th>نام محصول</th>
                                <th>وضعیت</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("wh.material.view_materials",$item->id)}}">{{$item->serial(1)}}</a>
                                    </td>
                                    <td>{{$item->product->code." - ".$item->product->caption}}</td>
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
