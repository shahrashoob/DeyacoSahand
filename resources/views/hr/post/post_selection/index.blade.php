@extends('layouts.admin._master')
@section("page_header_title"," کارتابل منابع انسانی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست پست ها
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th> #</th>
                                <th> عنوان پست</th>
                                <th>تنظیمات</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($posts as $item)
                                <tr>
                                    <td>{{++$row}}</td>

                                    <td>{{$item->caption}}</td>
                                    <td>
                                        <a href="{{route("hr.post.post_selection.create",$item->id)}}">تنظیمات گزینش</a>
                                    </td>


                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    {{--                    <div class="float-left">--}}
                    {{--                        نمايش رکوردهای--}}
                    {{--                        <b>{{$list->firstItem()}}</b>--}}
                    {{--                        تا--}}
                    {{--                        <b>{{$list->lastItem()}}</b>--}}
                    {{--                        از--}}
                    {{--                        <b>{{$list->total()}}</b>--}}
                    {{--                        رکورد موجود--}}
                    {{--                    </div>--}}
                    {{--                </div>--}}
                    {{--                <div class="text-center">--}}
                    {{--                    {{$list->links('pagination::bootstrap-4')}}--}}
                    {{--                </div>--}}
                </div>
            </div>

        </div>

        @endsection
        @section("styles")
            <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
            <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
