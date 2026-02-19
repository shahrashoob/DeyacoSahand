@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی - بافندگی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("goods_kind_process.fabric_raw.design_form.dashboard._search_view",["route"=>$route_path."index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست همه فرم های طراحی
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد </th>
                                <td>ماشین </td>
                                <td>وضعیت</td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route($route_path."view",$item)}}">{{$item->code}}</a>
                                    </td>

                                    <td>{{$item->machine->code??""}} - {{$item->machine->caption??""}}</td>
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
