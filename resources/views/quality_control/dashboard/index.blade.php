@extends('layouts.admin._master')
@section("page_header_title","داشبورد کنترل کیفیت ")
@section("content")
    <div class="row">

        @if(count($list_reject_product) >0)
            <div class="col-sm-12">
                {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
                <div class="card">
                    <div class="card-header">
                        <h5>لیست درخواست های مرجوعی</h5>

                    </div>
                    <div class="card-block">

                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>شماره فرم مرجوعی</th>
                                    <th>شماره برگ خروج</th>
                                    <th>شماره فرم ورود به انبار</th>
                                    <th>تاریخ ایجاد</th>
                                    <th>تعداد بسته بندی</th>
                                    <th>وضعیت</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $i=1;@endphp
                                @foreach($list_reject_product as $item)
                                    <tr>
                                        <td>{{$i++}}</td>
                                        <td>
                                            <a href="{{route("quality_control.dashboard.view_reject_product_form",$item)}}">
                                                {{$item->getCode()}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$item->exit_form->code}}
                                        </td>
                                        <td>
                                            {{$item->input->code??""}}
                                        </td>
                                        <td>{{$item->get_create_date_and_time()}}</td>
                                        <td>{{$item->items()->count()}}</td>
                                        <td>

                                            {{$item->status->caption}}

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="float-left">
                            نمايش رکوردهای
                            <b>{{$list_reject_product->firstItem()}}</b>
                            تا
                            <b>{{$list_reject_product->lastItem()}}</b>
                            از
                            <b>{{$list_reject_product->total()}}</b>
                            رکورد موجود


                        </div>
                    </div>
                    <div class="text-center">
                        {{$list_reject_product->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        @endif

        @if(count($list_input_forms))
            <div class="col-sm-12">
                {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
                <div class="card">
                    <div class="card-header">
                        <h5>لیست فرم های ورود به انبار </h5>

                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>

                                    <th>کد فرم</th>
                                    <th>تاریخ و ساعت</th>
                                    <th>حامل</th>
                                    <th>کاربر ایجاد کننده</th>
                                    <th>انبار</th>
                                    <th> وضعیت</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1;@endphp
                                @foreach($list_input_forms as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>
                                            <a href="{{route("quality_control.dashboard.view_input_form",$item)}}">
                                                {{$item->getCode()}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$item->get_create_date_and_time()}}
                                        </td>
                                        <td>{{$item->status_id ==500000200 ?$item->getCarrierCation():"---"}}</td>
                                        <td>
                                            <a href="{{route("wh.dashboard.show_form",$item)}}">{{$item->worker->fullname()}}</a>
                                        </td>
                                        <td>{{$item->warehouse->caption??""}}</td>
                                        <td>{{$item->status->caption??""}}</td>


                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(count($list_output_forms))
            <div class="col-sm-12">
                {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
                <div class="card">
                    <div class="card-header">
                        <h5>لیست فرم های خروج از انبار </h5>

                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>

                                    <th>کد فرم</th>
                                    <th>تاریخ و ساعت</th>
                                    <th>حامل</th>
                                    <th>کاربر ایجاد کننده</th>
                                    <th>انبار</th>
                                    <th> وضعیت</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1;@endphp
                                @foreach($list_output_forms as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>
                                            <a href="{{route("quality_control.dashboard.view_output_form",$item)}}">
                                                {{$item->getCode()}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$item->get_create_date_and_time()}}
                                        </td>
                                        <td>{{$item->status_id ==500000200 ?$item->getCarrierCation():"---"}}</td>
                                        <td>
                                            <a href="{{route("wh.dashboard.show_form",$item)}}">{{$item->worker->fullname()}}</a>
                                        </td>
                                        <td>{{$item->warehouse->caption??""}}</td>
                                        <td>{{$item->status->caption??""}}</td>


                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
