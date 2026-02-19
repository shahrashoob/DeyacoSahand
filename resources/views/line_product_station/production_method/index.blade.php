@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>لیست انواع روش های تولید

                        <a class="btn btn-success"
                           href="{{route("line_product_station.production_method.create")}}">
                            <i
                                    class="fa fa-plus"></i> افزودن روش تولید </a>

                        <a class="btn btn-outline-secondary"
                           href="{{route("line_product_station.line.index")}}">
                            بازگشت </a>

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive center">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد</th>
                                <th> توضیحات</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.production_method.edit",$item)}}">
                                            {{$item->getCode()}}
                                        </a>
                                    </td>
                                    <td>
                                        {!! nl2br($item->description) !!}
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
