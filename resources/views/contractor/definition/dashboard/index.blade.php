@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت پیمانکاران ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("contractor.definition.dashboard._search_view",["route"=>"contractor.definition.dashboard.index"])
            <div class="card">
                <div class="card-header">
                    <h5>
                        لیست پیمانکاران
                        <a class="btn btn-success" href="{{route("contractor.definition.dashboard.create")}}"><i class="fa fa-plus"></i> افزودن پیمانکار جدید </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد </th>
                                <th>نام پیمانکار  </th>
                                <th>نام و نام خانوادگی /نام شرکت </th>
                                <th> کد ملی/شناسه ملی</th>
                                <th>تعداد عملیات</th>
                                <th>مشخصه ها</th>
                                @if($edit_permission_software_system)
                                <th> تنظیمات سامانه جامع</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>

                                        <td>
                                            @if($edit_permission)
                                            <a href="{{route("contractor.definition.dashboard.edit",$item->id)}}" >{{$item->getCode()}}</a>
                                            @else
                                                {{$item->getCode()}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($edit_permission)
                                            <a href="{{route("contractor.definition.dashboard.edit",$item->id)}}" >{{$item->caption}}</a>
                                            @else
                                                {{$item->caption}}
                                            @endif
                                        </td>
                                    <td>
                                        @if($item->personal_type_id==1)
                                            {{$item->fullName()}}
                                        @else
                                            {{$item->company->caption??""}}
                                        @endif
                                    </td>

                                    <td>

                                        @if($item->personal_type_id==1)
                                            {{$item->worker->national_code??""}}
                                        @else
                                            {{$item->company->national_code??""}}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route("contractor.definition.operation.index",$item)}}">
                                            {{$item->operations()->count()}} عملیات
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{route("contractor.definition.property.index",$item)}}">
                                            {{$item->property()->count()}}  مشخصه
                                        </a>
                                    </td>
                                    @if($edit_permission_software_system)
                                    <td>
                                        <a href="{{ route('contractor.definition.dashboard.edit_software_system', $item) }}">
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
                <div class="text-center" >
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
