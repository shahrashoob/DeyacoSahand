@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
             @include("utility.public._search_view",["route"=>"wh.warehouse.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست  انبار ها
                        <a class="btn btn-success" href="{{route("wh.warehouse.create")}}"> <i
                                class="fa fa-plus"></i> افزودن انبار جدید </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد انبار</th>
                                <th>عنوان انبار</th>
                                <td>نوع انبار</td>
                                <td>گروه شیفت کاری انبار</td>
                                <td>ساختار فیزیکی انبار</td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("wh.warehouse.edit",$item)}}">{{$item->code}}</a>
                                    </td>
                                    <td>
                                        <a href="{{route("wh.warehouse.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>
                                        {{$item->warehouse_type->caption}}
                                    </td>
                                    <td>
                                        {{$item->shift->caption??""}}
                                    </td>

                                    <td>
                                        @if($item->warehouse_type_id==1)
                                            <a href="{{route("wh.warehouse_shelving.definition.index",$item)}}" ><i class="fa fa-th-large"></i> </a>
                                        @endif

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
