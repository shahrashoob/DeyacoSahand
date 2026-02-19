@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> مدیریت دسرسی به ایستگاه  در خط {{$line->caption}}</h5>
                </div>
                <div class="card-block">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-styling">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>دسترسی به ایستگاه</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($station_list as $item)
                                        <tr>
                                            <td>{{++$row}}</td>
                                            <td>
                                                @php $access_level=$item->get_access_level($post->id);@endphp
                                                @switch($access_level)
                                                    @case("full")
                                                    <i class="fas fa-circle fa-lg text-success"></i>
                                                    @break
                                                    @case("empty")
                                                    <i class="fas fa-circle-notch fa-lg "></i>
                                                    @break
                                                    @default
                                                    <i class="fas fa-adjust fa-lg text-warning"></i>
                                                    @break
                                                @endswitch

                                                <b>{{$item->fullCaption()}}</b>


                                            </td>

                                            <td>
                                                @if($access_level!="full")
                                                    <a href="{{route("hr.post.add_access",[$post,0,$item,0,0])}}" onclick="return confirm('آیا از ثبت دسترسی اطمینان دارید؟')"
                                                       class="btn btn-outline-success btn-sm">اجازه دسترسی کامل </a>
                                                    <a href="{{route("hr.post.manage_access",[$post,0,$item,0,0])}}"
                                                       class="btn btn-outline-primary btn-sm">مدیریت دسترسی ها </a>
                                                @endif
                                                @if($access_level!="empty")
                                                    <a href="{{route("hr.post.remove_access",[$post,0,$item,0,0])}}"
                                                       class="btn btn-outline-danger btn-sm" onclick="return confirm('آیا از حذف دسترسی اطمینان دارید؟')"
                                                    >حذف کامل دسترسی ها </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>

                            </div>

                        </div>

                        <a href="{{route("hr.post.edit",$post)}}" class="btn btn-outline-dark">بازگشت</a>

                </div>
            </div>
        </div>

    </div>
@endsection
