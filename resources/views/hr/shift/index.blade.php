@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>لیست شیفت های سازمان
                        <a class="btn btn-success" href="{{route("hr.shift.create")}}">
                            <i
                                class="fa fa-plus"></i> افزودن شیفت جدید </a>

                        <a class="btn btn-primary" href="{{route("hr.shift.calc_entry_log_for_days")}}">
                            <i
                                    class="fa fas fa-redo-alt "></i> محاسبه مجدد کارکرد پرسنل </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد</th>
                                <th> عنوان</th>
                                <th> تعداد گروه  شیفت</th>
                                <th>میانگین ساعت کار قانونی در روز (دقیقه)</th>
                                <th> دسته بندی ها</th>
                                <th>وضعیت</th>
                                <th>بارگذاری تقویم کاری</th>
                                <th>آخرین بروز رسانی</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->getCode()}}
                                    </td>
                                    <td>
                                        <a href="{{route("hr.shift.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>{{$item->number_of_shift_work??""}}</td>
                                    <td>{{$item->legal_working_hours_in_minute??""}}</td>
                                    <td>
                                        <a href="{{route("hr.shift.edit_shift_group",$item)}}"> {{$item->getShiftWorkGroupCount()}} دسته بندی </a>

                                    </td>
                                    <td>{{$item->active_status->caption??""}}</td>
                                    <td>
                                        <a href="{{route("hr.shift.upload_shift_work",$item)}}"><i class="fa fa-upload"></i> </a>
                                    </td>
                                    <td>
                                        {{$item->create_datetime()}}
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
