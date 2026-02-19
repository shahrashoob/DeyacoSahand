@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> لیست همه ماده های قراداد</h5>
                    <a class="btn btn-outline-success  " href="{{route("accounting.contract.clause.create")}}"> <i
                                class="fa fa-plus"></i> افزودن ماده جدید </a>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th> عنوان</th>

                                <th>بند های ماده</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->caption}}</td>

                                    <td>
                                        <a href="{{route("accounting.contract.clause.add_clause_article",$item)}}">
                                            شامل
                                            {{$item->clause_articles()->count()}}
                                            بند
                                        </a>
                                        <a class="text-success"
                                           href="{{route("accounting.contract.clause.add_clause_article",$item)}}">
                                            <i class="fa fa-plus-circle"></i>
                                        </a>
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                        <a href="{{route("accounting.contract.contract.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>
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

