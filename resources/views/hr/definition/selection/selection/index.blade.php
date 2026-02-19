@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5> لیست گزینش ها
                        <a class="btn btn-success" href="{{route("hr.definition.selection.selection.create")}}"> <i
                                class="fa fa-plus"></i> افزودن گزینش جدید </a>

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th> عنوان گزینش</th>
                                <th> نوع گزینش</th>
                                <th> وضعیت</th>
                                <th> جمع وزن شاخص ها</th>
                                <th> محتوای گزینش</th>


                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($selections as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("hr.definition.selection.selection.edit",$item)}}">{{$item->caption?? ""}}</a>
                                    </td>
                                    <td>{{$item->selection_type->caption?? ""}}</td>

                                    <td>{{$item->status->caption}}</td>
                                    <td>{{$item->selection_indicators()->sum('weight')}}</td>
                                    <td>
                                        <a href="{{route("hr.definition.selection.selection_indicator.create",$item->id)}}">
                                            {{$item->selection_indicators->count()}}
                                            شاخص
                                        </a>

                                    </td>
                                    {{--                                    <td>--}}
                                    {{--                                        <a href="{{ route('hr.post.post.add_selector', ['selection' => $item->id]) }}">--}}

                                    {{--                                            {{$item->selection_selector_post->count()}}--}}
                                    {{--                                            پست سازمانی و--}}
                                    {{--                                            {{$item->selection_selector_committee->count()}}--}}
                                    {{--                                            کمیته--}}
                                    {{--                                        </a>--}}


                                    {{--                                    </td>--}}
                                    <td>
                                        <a href="{{route("hr.definition.selection.selection.destroy",$item)}}"
                                           onclick="return confirm('آیا از حذف گزینش اطمینان دارید؟')"><i
                                                class="fa fa-trash text-danger"></i> </a>
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
