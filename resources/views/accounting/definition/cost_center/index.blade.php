@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("utility.public._search_view",["route"=>"accounting.definition.cost_center.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست مراکز هزینه
                        <a class="btn btn-success" href="{{route("accounting.definition.cost_center.create")}}"> <i
                                class="fa fa-plus"></i> افزودن مرکز هزینه </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد مرکز هزینه</th>
                                <th> عنوان مرکز هزینه</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("accounting.definition.cost_center.edit",$item)}}">{{$item->code}}</a>
                                    </td>
                                    <td>
                                        <a href="{{route("accounting.definition.cost_center.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>{{$item->status->caption??""}}</td>
                                    <td>
                                        <a href="{{route("accounting.definition.cost_center.destroy",$item)}}"
                                           onclick="return confirm('آیا از حذف مرکز هزینه اطمینان دارید؟')"><i
                                                class="fa fa-trash text-danger"></i> </a>
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
