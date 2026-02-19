@extends('layouts.admin._master')
@section("page_header_title","داشبورد خروج از انبار ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("warehouse.out.dashboard._search_view",["route"=>"wh.out.dashboard.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست فرم های تحویل کالا</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد فرم</th>
                                <th>تاریخ ایجاد</th>
                                <th>کاربر ایجاد کننده فرم</th>
                                <th>درخواست دهنده</th>
                                <th>شماره مرجع</th>
                                <th>مجوز بارگیری</th>
                                <th>انبار</th>
                                <th>تعداد بسته بندی<br/> حمل و نقل</th>
                                <th> وضعیت</th>

                                <th></th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr {{$item->active_status_id ==7005102?"style=background:#1e3953":""}}>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("wh.out.dashboard.view",$item)}}/{{$list->currentPage()}}">{{$item->getCode()}}</a>
                                    </td>
                                    <td>{{$item->get_create_date_and_time()}}</td>
                                    <td>{{$item->worker->fullname()}}</td>
                                    <td>{{$item->applicant->fullCaption()}}  </td>
                                    <td>
                                        @if($item->applicant_type_id == 30)
                                            <a href="{{route("wh.out.dashboard.view_order",$item)}}">{{$item->getReferenceNumber()}}</a>
                                        @else
                                            {{$item->getReferenceNumber()}}
                                        @endif
                                    </td>
                                    <td>
                                        {{$item->order && $item->applicant_type_id == 30?$item->order->loading_date():""}}
                                    </td>
                                    <td>{{$item->warehouse->caption??""}}</td>
                                    <td>{{$item->transport_items()->count()}}</td>
                                    <td>{{$item->getStatus()}}</td>
                                    <td>
                                        <a href="{{route("wh.transport.dashboard.download_transport",$item)}}"><i
                                                class="fa fa-download"></i> </a>
                                    </td>
                                    <td>
                                        <a href="{{route("wh.transport.dashboard.download_report1",$item)}}"> <i
                                                class="fa fa-download"></i> </a>
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
