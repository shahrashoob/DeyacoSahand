@extends('layouts.admin._master')
@section("page_header_title","کارتابل منابع انسانی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست آموزش ها
                        <a class="btn btn-success" href="{{route("hr.definition.education.education.create")}}"> <i
                                class="fa fa-plus"></i> افزودن آموزش جدید </a>

                        {{--                        <a class="btn btn-primary" href=""> <i--}}
                        {{--                                class="fa fa-cog"></i> تنظیمات </a>--}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th> #</th>
                                <th> عنوان آموزش</th>
                                <th> نوع آموزش</th>
                                <th> نوع سنجش</th>
                                <th> فیلم متنی</th>
                                <th> فیلم ویدیویی</th>
                                <th> حداقل امتیاز برای تایید آموزش</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($educations as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("hr.definition.education.education.edit",$item)}}">{{$item->caption?? ""}}</a>
                                     </td>
                                    <td>{{$item->education_type->caption?? ""}}</td>
                                    <td>{{$item->exam_type->caption?? ""}}</td>
                                    <td>
                                        @if($item->educational_text_file_id)
                                        <a href="{{ route('hr.definition.education.education.download',[ $item->id,$item->educational_text_file_id])}}">{{ $item->educational_text_file->caption??" "}}</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->educational_video_file_id)
                                            <a href="{{ route('hr.definition.education.education.download',[ $item->id,$item->educational_video_file_id])}}">{{ $item->educational_video_file->caption??" "}}</a>
                                        @endif
                                    </td>
{{--                                    <td>{{$item->educational_video_file->caption??""}}</td>--}}
                                    <td>{{$item->minimum_score_to_confirm_the_education??""}}</td>

                                    <td>
                                        <a href="{{route("hr.definition.education.education.destroy",$item)}}"
                                           onclick="return confirm('آیا از حذف آموزش اطمینان دارید؟')"><i
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
