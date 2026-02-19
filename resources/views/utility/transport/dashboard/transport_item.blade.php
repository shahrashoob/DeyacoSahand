@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست کالاهای داخل بار</h5>
                </div>
                <div class="card-block">
                    @include("utility.transport.dashboard._table_product_list")
                </div>
            </div>
        </div>
            <div class="col-sm-12">
                {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
                <div class="card">
                    <div class="card-header">
                        <h5>لیست بسته بندی های ارسال بار
                            {{$transport->getCode()}}
                            @if($transport->order)
                                -
                                سفارش {{$transport->order->code()}}
                            @endif
                        </h5>

                        <a href="{{route("utility.transport.dashboard.create_transport_item",$transport)}}"
                           class="btn btn-primary">افزودن بسته بندی جدید </a>

                        <a href="{{route("utility.transport.dashboard.set_to_warehouse",$transport)}}"
                           class="btn btn-success">ثبت تایید انبار به صورت جمعی </a>

                        <a href="{{route("utility.transport.dashboard.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>
                    </div>
                    <div class="card-block">

                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>کد</th>
                                    <th> بسته بندی ارسال بار</th>
                                    <th>تعداد بسته بندی</th>
                                    <th>تاریخ ایجاد</th>
                                    <th>وضعیت</th>
                                    <th></th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($transport->items as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>{{$item->id}}</td>
                                        <td>
                                            <a href="{{route("utility.transport.dashboard.packing_list",$item)}}">
                                                {{$item->code()}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$item->transport_packing_list()->count()}}
                                        </td>
                                        <td>
                                            {{$item->create_datetime()}}
                                        </td>
                                        <td>{{$item->status->caption}}</td>
                                        <th>
                                            <a
                                               href="{{route("utility.transport.dashboard.transport_confirm",[$item,"print_back"])}}">
                                                <i class="fa fa-print"></i>
                                            </a>

                                            <a href="{{route("utility.transport.dashboard.download",$item)}}">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <a href="{{route("utility.transport.dashboard.transport_item_delete",$item)}}"
                                               class="text-danger"
                                               onclick="return confirm('آیا از حذف اطمینان دارید؟')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </th>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        @endsection
        @section("styles")
            <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
            <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
