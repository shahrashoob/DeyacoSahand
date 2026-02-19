@extends('layouts.admin._master')
@section("page_header_title","کارتابل جاری وصول مطالبات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
{{--            @include("orders._search_view",["route"=>"sales.receipt_of_receivables.list"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست سفارش ها</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد سفارش </th>
                                <th>کانال توزیع</th>
                                <th>نام مرکز  </th>
                                <th>تعداد روز در<br/> انتظار ارسال </th>
                                <th> اولویت سفارش </th>
                                <th>  وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("sales.receipt_of_receivables.finished_order",$item->id)}}" >{{$item->code()}}</a>
                                    </td>
                                    <td>{{$item->customer->channelType->caption??""}}</td>
                                    <td>{{$item->customer->caption??""}}</td>
                                    <td>{{$item->number_of_days_waiting()}}</td>
                                    <td>{{$item->priority->caption??""}}</td>
                                    <td>{{$item->status->caption??""}}</td>


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
