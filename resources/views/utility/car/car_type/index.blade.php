@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست   انواع خودرو ها

                    </h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> نام خودرو </th>
                                <th> حداقل وزن (تن) </th>
                                <th> حداکثر وزن (تن) </th>
                                <th> حداقل حجم (متر مکعب) </th>
                                <th> حداکثر حجم  (متر مکعب)</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("utility.car.car_type.edit",$item)}}">{{$item->caption}}</a>
                                    </td>

                                    <td>{{$item->min_weight??""}}</td>
                                    <td>{{$item->max_weight??""}}</td>
                                    <td>{{$item->min_volume??""}}</td>
                                    <td>{{$item->max_volume??""}}</td>
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
