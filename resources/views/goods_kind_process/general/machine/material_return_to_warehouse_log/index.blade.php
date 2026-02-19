@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>سابقه برگشت مواد اولیه {{$machine->fullCaption()}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>شماره درخواست</th>
                                <th>تاریخ و ساعت</th>
                                <th>اقدام کننده</th>
                                <th> وضعیت</th>
                                <th> تخصیص جاری ماشین</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        @if($item->status_id == 6021001)
                                            <a href="{{route(str_replace("_log","",$route_path)."index",[$machine])}}">
                                                {{$item->machine_allocation_modification_type->caption}} شماره
                                                {{$item->id}}</a>
                                            @else
                                            <a href="{{route($route_path."details",[$machine,$item])}}">
                                                {{$item->machine_allocation_modification_type->caption}} شماره
                                                {{$item->id}}</a>
                                        @endif

                                    </td>
                                    <td>
                                        {{$item->create_date()}}
                                    </td>
                                    <td>{{$item->worker?$item->worker->fullname():""}}</td>
                                    <td>{{$item->status->caption}}</td>
                                    <td>{{$item->allocation_id}}</td>

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
        <div class="col-md-12 center">
            <a class="btn btn-outline-dark" href="{{route($dashboard_route."view",$machine)}}">بازگشت</a>
        </div>
    </div>

@endsection
@section("styles")

@endsection
