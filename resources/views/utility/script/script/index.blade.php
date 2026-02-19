@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست دستیارهای هوشمند
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نام</th>
                                <th> عنوان</th>
                                <th>برنامه اجرا (cron)</th>
                                <th>آخرین زمان اجرا</th>
                                <th>زمان اجرای بعدی</th>
                                <th>سابقه اجرا</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->caption_en}}</td>
                                    <td>
                                        @if($post_script[$item->id]->allow_edit )
                                            <a href="{{route("utility.script.".$item->code.".edit",$item)}}">{{$item->caption}}</a>
                                        @else
                                            {{$item->caption}}
                                        @endif
                                    </td>
                                    <td>{{$item->cron}}</td>
                                    <td>{{$item->get_last_run_datetime()}}</td>
                                    <td>{{$item->get_next_run_datetime()}}</td>

                                        <td>
                                            @if($post_script[$item->id]->allow_view_log)
                                            <a href="{{route("utility.script.log",$item)}}">
                                                {{$item->logs()->orderByDesc("id")->first()->event->caption??""}}

                                            </a>
                                            @endif
                                        </td>

                                    <td>
                                        @if($item->run_status_id == 410300)
                                            <span class="text-danger">درحال اجرا</span>
                                        @elseif($item->run_status_id == 410500)
                                            <span class="text-warning">راه اندازی اتومات</span>
                                        @else
                                            {{$item->active_status->caption??""}}
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
