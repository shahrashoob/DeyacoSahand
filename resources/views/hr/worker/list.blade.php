@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("hr.worker._search_view",["route"=>"hr.worker.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست شاغلین ها</h5>
                    @if($post_user->checkButtonPermission("hr.worker.edit"))
                        <a href="{{route('hr.worker.create')}}" class="btn btn-outline-primary">افزودن کاربر جدید</a>
                    @endif
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نام و نام خانوادگی</th>
                                <th>کد ملی</th>
                                <th>نوع همکاری</th>
                                <th>تاریخ قرارداد</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php
                                $permission_personal_index=$post_user->checkButtonPermission("hr.personal.index");
                                $permission_personal_info_index=$post_user->checkButtonPermission("hr.worker.personal_info.index");
                                $permission_worker_edit=$post_user->checkButtonPermission("hr.worker.edit");
                                $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        @if($permission_personal_index)
                                            <a href="{{route("hr.personal.index",[$item,$item->getRandom(),"hr.worker.index"])}}">{{$item->fullname()}}</a>
                                        @else
                                            {{$item->fullname()}}
                                        @endif
                                    </td>
                                    <td>
                                        {{$item->national_code}}
                                    </td>
                                    <td>
                                        {{$item->cooperation_type->caption}}
                                    </td>
                                    <td>
                                        {{$item->end_date_of_contract()}}
                                    </td>
                                    <td>
                                        {{$item->status->caption}}
                                    </td>
                                    <td>
                                        @if($permission_personal_info_index)
                                        <a href="{{route("hr.worker.personal_file.index",$item)}}" title="پرونده پرسنلی">
                                            <i class="fas fa-address-card"></i>
                                        </a>
                                        @endif
                                        <a href="{{route("hr.worker.print_worker_card",$item)}}"
                                           title="پرینت کارت ویزیت" onclick="return confirm('آیا از پرینت کارت پرسنلی اطمینان دارید؟')">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if($permission_worker_edit)
                                            <a href="{{route("hr.worker.edit",$item)}}" title="ویرایش اطلاعات شاغل">
                                                <i class="fa fa-edit"></i>
                                            </a>
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
