@extends('layouts.admin._master')
@php $permission_confirm_packing=$post_user->checkButtonPermission("wh.production_warehouse.dashboard.confirm_packing");@endphp
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست برگ های خروج در انتظار تایید انبارک ها

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>تاریخ</th>
                                <th>کد درخواست</th>
                                <th>کد برگ خروج</th>
                                <th>انبارک تولید</th>
                                <th></th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->get_create_date_and_time()}}
                                    </td>
                                    <td>
                                        {{$item->product_request_form_code}}
                                    </td>
                                    <td>
                                        {{$item->code}}

                                    </td>
                                    <td>
                                        {{$item->warehouse_caption}}
                                    </td>

                                    <td>
                                        @if($permission_confirm_packing)
                                            @if($material_delivery_confirmation)
                                                <a class="btn btn-success btn-sm"
                                                   href="{{route("wh.production_warehouse.dashboard.confirm",[$item->id,$item->product_request_form_id,$item->warehouse_id])}}">
                                                    تایید تحویل
                                                </a>
                                            @endif

                                            @if($material_delivery_reject)
                                                <a class="btn btn-danger btn-sm"
                                                   href="{{route("wh.production_warehouse.dashboard.reject",[$item->id,$item->product_request_form_id,$item->warehouse_id])}}">
                                                    عدم تایید تحویل
                                                </a>

                                            @endif
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
