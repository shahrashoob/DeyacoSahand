@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5> لیست مصاحبه ها
                        <a class="btn btn-success" href="{{route("hr.defenition.interview.create")}}"> <i
                                class="fa fa-plus"></i> افزودن مصاحبه جدید </a>

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th> #</th>
                                <th> عنوان</th>
                                <th> عنوان مصاحبه</th>
                                <th> نوع مصاحبه</th>
                                <th>مصاحبه کننده</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($interviews as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("hr.defenition.interview.edit",$item)}}">ویرایش</a>
                                    </td>
                                    <td>{{$item->caption?? ""}}</td>
                                    <td>{{$item->interview_type->caption?? ""}}</td>
                                    <td>
                                        <a href="{{ route('hr.defenition.interview.add_post_innterviewer', ['interview' => $item->id]) }}"> پست </a>
                                        <a href="{{ route('hr.defenition.interview.add_floatingpost_Type_innterviewer', ['interview' => $item->id]) }}">پست شناور  </a>
                                        <a href="{{ route('hr.defenition.interview.add_committee_innterviewer', ['interview' => $item->id]) }}"> کمیته </a>

                                    </td>
                                    <td>
                                        <a href="{{route("hr.defenition.interview.destroy",$item)}}"
                                           onclick="return confirm('آیا از حذف مصاحبه اطمینان دارید؟')"><i
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
