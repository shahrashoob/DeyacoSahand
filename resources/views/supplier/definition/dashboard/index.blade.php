@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت تامین کنندگان ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("supplier.definition.dashboard._search_view",["route"=>"supplier.definition.dashboard.index"])
            <div class="card">
                <div class="card-header">
                    <h5>
                        لیست تامین کنندگان
                        <a class="btn btn-success" href="{{route("supplier.definition.dashboard.create")}}"><i
                                    class="fa fa-plus"></i> افزودن تامین کننده جدید </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد</th>
                                <th>نام تامین کننده</th>
                                <th>نام و نام خانوادگی/نام شرکت</th>
                                <th> کد ملی / شناسه ملی</th>
                                <th>نوع تامین کننده</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        @if($edit_permission)
                                            <a href="{{route("supplier.definition.dashboard.edit",$item->id)}}">{{$item->getCode()}}</a>
                                        @else
                                            {{$item->getCode()}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($edit_permission)
                                            <a href="{{route("supplier.definition.dashboard.edit",$item->id)}}">{{$item->caption}}</a>
                                        @else
                                            {{$item->caption}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->personal_type_id==1)
                                            {{$item->user->firstname." ".$item->user->lastname??""}}
                                        @else
                                            {{$item->company->caption??""}}
                                        @endif
                                    </td>

                                    <td>

                                        @if($item->personal_type_id==1)
                                            {{$item->user->national_code??""}}
                                        @else
                                            {{$item->company->national_code??""}}
                                        @endif
                                    </td>

                                    <td>{{$item->supplier_type->caption??""}}</td>

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
