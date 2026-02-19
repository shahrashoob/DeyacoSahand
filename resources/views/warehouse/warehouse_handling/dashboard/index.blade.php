@extends('layouts.admin._master')
@section("page_header_title","داشبورد انبار ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>لیست دستور های انبار گردانی </h5>
                    @if ($post_user->checkButtonPermission( "wh.warehouse_handling.new_handling.index"))
                        <a href="{{route("wh.warehouse_handling.new_handling.index")}}"> <i class="fa fa-plus"></i>
                            دستور  انبار گردانی جدید</a>
                    @endif
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>

                                <th>کد</th>
                                <th>انبار</th>
                                <th>تاریخ شروع</th>
                                <th>تاریخ پایان</th>
                                <th>ایجاد کننده</th>
                                <th> وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>
                                        {{$item->getCode()}}
                                    </td>

                                    <td>
                                        <a href="{{route("wh.warehouse_handling.dashboard.view",[$item,$list->currentPage()])}}">{{$item->warehouse->caption}}</a>
                                    </td>
                                    <td>
                                        {{$item->start_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->end_datetime()}}
                                    </td>

                                    <td>{{$item->worker->fullname()}}</td>
                                    <td>{{$item->status->caption??""}}</td>
                                    <td></td>

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
