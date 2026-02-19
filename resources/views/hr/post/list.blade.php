@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("hr.post._search_view",["route"=>"hr.post.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست پست ها</h5>
                    @if($post_user->checkButtonPermission("hr.post.create"))
                        <a href="{{route('hr.post.create')}}" class="btn btn-outline-primary">افزودن پست جدید</a>
                    @endif

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد پست</th>
                                <th> عنوان پست</th>
                                <th>پست مافوق</th>
                                <th>رده سازمانی</th>
                                <th>نوع شیفت</th>
                                @if($digital_setting || $smart_object_setting ||$evaluation_setting || $chat_setting ||$employment_setting)
                                    <th>عملیات</th>
                                @endif
                                <th>شاغلان</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->id}}</td>
                                    <td>
                                        @if($post_user->checkButtonPermission("hr.post.edit"))
                                            <a href="{{route("hr.post.edit",$item)}}">{{$item->caption}}</a>
                                        @else
                                            {{$item->caption}}
                                        @endif
                                        @if($item->is_system_supervisor)
                                            <i class="fa fa-star text-warning" title="ناظر سیستم"></i>
                                        @endif
                                    </td>

                                    <td>{{$item->parent->caption??""}}</td>
                                    <td>{{$item->organization_category->caption??""}}</td>
                                    <td>{{$item->shift->caption??""}}</td>

                                    <td>
                                        @if($digital_setting || $smart_object_setting ||$evaluation_setting || $chat_setting ||$employment_setting)
                                            <button class="btn drp-icon btn-outline-primary dropdown-toggle"
                                                    type="button" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="feather icon-settings center"></i></button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if($digital_setting)
                                                    <a class="dropdown-item text-right d-flex align-items-center justify-content-between"
                                                       href="{{route("hr.post.edit_setting",$item)}}">
                                                        <span><i class="fa fa-user-cog"></i>  قوانین دیجیتال </span>

                                                    </a>
                                                @endif
                                                @if($smart_object_setting)
                                                    <a class="dropdown-item text-right d-flex align-items-center justify-content-between"
                                                       href="{{route("hr.post.smart_object.index",$item)}}">
                                                        <span><i class="fa fa-user-astronaut"></i>  اشیا هوشمند </span>

                                                    </a>
                                                @endif
                                                @if($evaluation_setting)
                                                    <a class="dropdown-item text-right d-flex align-items-center justify-content-between"
                                                       href="{{route("hr.post.post_evaluation.index",$item)}}">
                                                        <span><i class="fa fa-chart-line"></i>  ارزیابی عملکرد </span>

                                                    </a>
                                                @endif
                                                @if($employment_setting)
                                                    <a class="dropdown-item text-right d-flex align-items-center justify-content-between"
                                                       href="{{route("hr.post.employment.index",$item)}}">
                                                        <span><i class="fa fas fa-handshake"></i>  تنظیمات جذب </span>

                                                    </a>
                                                @endif
                                                @if($chat_setting)
                                                    <a class="dropdown-item text-right d-flex align-items-center justify-content-between"
                                                       href="{{route("hr.post.chat.index",$item)}}">
                                                        <span><i class="fa fas fa-comment"></i>  گفتگوی برخط </span>

                                                    </a>
                                                @endif
                                                @if($entry_status_permission)
                                                    <a class="dropdown-item text-right d-flex align-items-center justify-content-between"
                                                       href="{{route("hr.post.entry_status_permission.index",$item)}}">
                                                        <span><i class="fa fa-sign-in-alt"></i>  تنظیمات ورود به سامانه </span>

                                                    </a>
                                                @endif
                                            </div>

                                        @endif
                                    </td>

                                    <td>
                                        @if(!in_array($item->id,[1100,1200,1300]))
                                            @php $k=0;@endphp
                                            @foreach($item->worker as $worker)
                                                @if($delete_user)
                                                    <a href="{{route("hr.post.delete_user",[$item,$worker])}}"
                                                       onclick="return confirm('آیا از حذف پست باری شاغل انتخاب شده اطمینان دارید؟')"><i
                                                                class="fa fa-trash text-danger"></i>

                                                        {{$worker->fullname()}} </a>
                                                    ({{$shift_work[$worker->pivot->shift_work_id??0]}})  ,
                                                @else
                                                    {{$worker->fullname()}}
                                                    ({{$shift_work[$worker->pivot->shift_work_id??0]}}),
                                                @endif
                                                @if(++$k%3==0)
                                                    <br/>
                                                @endif
                                            @endforeach


                                            @if($add_user )
                                                <a href="{{route("hr.post.add_user",$item)}}">
                                                    <i class="fa fa-plus text-success"></i>
                                                </a>

                                            @endif
                                        @else
                                           {{$item->worker()->count()}} نفر
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
