@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت مشتریان ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("customer.definition.admin._search_view",["route"=>"customer_group.definition.admin.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست مشتریان</h5>
                    <a href="{{route("customer_group.definition.admin.create")}}" class="btn btn-outline-success">افزودن
                        مشتری جدید</a>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد مرکز</th>
                                <th>نام مرکز</th>
                                <th>نام و نام خانوادگی</th>
                                <th> کانال توزیع</th>
                                <th>نوع مشتری</th>
                                @if($edit_permission_software_system)
                                <th> تنظیمات سامانه جامع</th>
                                @endif
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        @if($edit_permission)
                                            <a href="{{route("customer_group.definition.admin.edit",$item->id)}}">{{$item->code}}</a>
                                        @else
                                            {{$item->code}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($edit_permission)
                                        <a href="{{route("customer_group.definition.admin.edit",$item->id)}}">{{$item->caption}}</a>
                                        @else
                                            {{$item->caption}}
                                        @endif
                                    </td>
                                    <td>{{isset($item->user)? $item->user->fullname() : " ***"}}</td>
                                    <td>{{$item->channelType->caption??""}}</td>
                                    <td>{{$item->parent?"مشتری سطح 2"." (معرفی شده توسط ".$item->parent->caption.")":"مشتری سطح 1"}}</td>
                                    @if($edit_permission_software_system)
                                        <td>
                                            <a href="{{ route('customer_group.definition.admin.edit_software_system', $item->id) }}">
                                                <i class="feather icon-settings"></i>
                                            </a>
                                        </td>
                                    @endif

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
