@extends('layouts.admin._master')
@section("page_header_title"," داشبورد نگهداری و تعمیرات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("line_product_station.maintenance.dashboard._search_view",["route"=>$route_path."index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست همه درخواست ها
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center" >
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>شماره درخواست</th>
                                <td> تاریخ درخواست</td>
                                <td>نوع درخواست</td>
                                <td>ماشین</td>
                                <td>وضعیت</td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route($route_path."view",$item)}}">{{$item->getCode()}}</a>
                                    </td>


                                    <td>{{$item->get_create_date_and_time()}} </td>
                                    <td>{{$item->maintenance_type->caption??""}} </td>
                                    <td>{{$item->machine->fullCaption()}}</td>
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
