@extends('layouts.admin._master')
@section('page_header_title'," داشبورد روابط عمومی")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست Pup Up ها

                        <a href="{{route("utility.pup_up.admin.create")}}"
                           class="btn btn-outline-success">افزودن Pup Up جدید</a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> عنوان</th>
                                <th>تاریخ شروع</th>
                                <th>تاریخ پایان</th>
                                <th>تعداد نمایش</th>
                                <th>ورژن</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->caption}}
                                    </td>
                                    <td>
                                        {{$item->start_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->end_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->number_of_show}}
                                    </td>
                                    <td>
                                        {{$item->version}}
                                    </td>

                                    <td>
                                        <a href="{{ route("utility.pup_up.admin.edit",$item)}}"><i
                                                class="fa fa-edit"></i> </a>
                                        <a class="text-danger" onclick="return confirm('آیا از حذف اطیمنان دارید؟')" href="{{ route("utility.pup_up.admin.destroy",$item)}}"><i
                                                class="fa fa-trash"></i> </a>

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
            <div>

            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
