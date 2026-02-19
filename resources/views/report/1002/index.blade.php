@extends('layouts.admin._master')
@section("page_header_title","گزارش 1002 - لیست کارت های ثبت شده")

@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("production.production_card._search_view",["route"=>"report.1002.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست کارت های در ثبت شده</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>سریال تولید</th>
                                <th>نام محصول</th>
                                <th>تعداد</th>
                                <th>واحد</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("report.1002.view_card",$item->production_card_id)}}">{{$item->serial(1)}}</a>
                                    </td>
                                    <td>{{$item->product->code." - ".$item->product->caption}}</td>
                                    <td>{{$item->number}}</td>
                                    <td>{{$item->product->unit->bach_caption}}</td>
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
